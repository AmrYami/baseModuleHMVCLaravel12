import dotenv from 'dotenv';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import express from 'express';
import redis from 'redis';
import http from 'http';
import https from 'https';
import jwt from 'jsonwebtoken';
import { Server as SocketIo } from 'socket.io';

// Load environment variables relative to this script (NOT the CWD).
// In ESM, __dirname is not defined; derive it from import.meta.url so
// process managers (PM2, systemd, Docker entrypoint) starting the server
// from any directory still resolve the project's .env correctly.
const __dirname = path.dirname(fileURLToPath(import.meta.url));
dotenv.config({ path: path.join(__dirname, '../.env') });

const PORT = process.env.PORT_NOTIFICATIONS || 8890;
const HTTPS_ENABLED = String(process.env.FORCE_HTTPS || 'false').toLowerCase() === 'true';

const app = express();

// ✅ Add middleware to parse JSON and URL-encoded payloads
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Setup SSL if HTTPS is enabled
let server;
if (HTTPS_ENABLED) {
    try {
        const options = {
            key: fs.readFileSync(process.env.SSL_KEY, 'utf8'),
            cert: fs.readFileSync(process.env.SSL_CERT, 'utf8'),
            // SSL_CA is only needed for mutual TLS / client cert verification.
            // Optional: include it only when the env var is provided.
            ...(process.env.SSL_CA ? { ca: fs.readFileSync(process.env.SSL_CA, 'utf8') } : {}),
        };
        server = https.createServer(options, app);
    } catch (error) {
        console.error("❌ SSL Error:", error.message);
        process.exit(1);
    }
} else {
    server = http.createServer(app);
}

// Initialize Socket.io with proper CORS settings.
// Browsers reject `Access-Control-Allow-Origin: *` when `credentials: true`,
// so when APP_URL is unset we explicitly fail closed (`origin: false`) to
// surface the misconfiguration rather than silently break long-polling.
const io = new SocketIo(server, {
    cors: {
        origin: process.env.APP_URL || false,
        methods: ["GET", "POST"],
        credentials: true
    }
});

// Redis configuration; converting port to a number for safety
const redisConfig = {
    socket: {
        host: process.env.REDIS_HOST || '127.0.0.1',
        port: process.env.REDIS_PORT ? Number(process.env.REDIS_PORT) : 6379,
        tls: process.env.REDIS_TLS === 'true' ? {} : undefined
    },
    password: process.env.REDIS_PASSWORD || undefined
};

// ✅ Encapsulate Redis publisher initialization
async function initializeRedisPublisher() {
    const client = redis.createClient(redisConfig);
    client.on('error', (err) => console.error("🚨 Redis Error:", err));
    await client.connect();
    console.log("✅ Redis Publisher Connected");
    return client;
}

// Initialize the Redis publisher client (for sending messages)
const redisPublisher = await initializeRedisPublisher();

// ✅ Graceful shutdown: handle SIGINT/SIGTERM to close connections properly
function setupGracefulShutdown(redisPublisherClient) {
    const shutdown = async () => {
        console.log("🔌 Shutting down gracefully...");
        try {
            if (redisPublisherClient.isOpen) {
                await redisPublisherClient.quit();
                console.log("✅ Redis Publisher closed successfully.");
            } else {
                console.log("ℹ️ Redis Publisher already closed.");
            }
        } catch (error) {
            console.error("Error during Redis publisher shutdown:", error);
        }
        // Close Socket.io first so existing socket connections are torn down
        // and stop holding the HTTP server open. Without io.close(), SIGINT
        // /SIGTERM can hang indefinitely during deploys.
        io.close(() => {
            server.close(() => {
                console.log("Server closed.");
                process.exit(0);
            });
        });
    };

    process.on('SIGINT', shutdown);
    process.on('SIGTERM', shutdown);
}

setupGracefulShutdown(redisPublisher);


// Start server
server.listen(PORT, () => {
    console.log(`🚀 Server running on ${HTTPS_ENABLED ? 'https' : 'http'}://localhost:${PORT}`);
});

// Socket.io connection handling
io.on('connection', async (socket) => {
    console.log("🔗 New client connected:", socket.id);

    // ✅ Each socket gets its own Redis subscriber for isolating message handling.
    // Wrap connect() so a transient Redis outage doesn't propagate to the
    // global unhandledRejection handler and process.exit(1) the whole server.
    const subscriber = redis.createClient(redisConfig);
    subscriber.on('error', (err) => console.error("🚨 Redis Subscriber Error:", err));

    try {
        await subscriber.connect();
        console.log("✅ Redis Subscriber Connected for", socket.id);
    } catch (err) {
        console.error(`🚨 Failed to connect Redis subscriber for ${socket.id}:`, err.message);
        socket.emit('error', 'Server temporarily unavailable');
        socket.disconnect(true);
        return;
    }

    try {
        await subscriber.subscribe('message', (message, channel) => {
            console.log(`📩 Received message on ${channel}:`, message);

            try {
                const data = JSON.parse(message);
                if (data.channel === 'notifications') {
                    // Deliver only to sockets in the target user's room.
                    // Clients should join `user-${userId}` after authentication.
                    const eventName = `${channel}-notifications-${data.user_id}`;
                    io.to(`user-${data.user_id}`).emit(eventName, data.data);
                    console.log(`📩 Sent message to ${eventName} (room user-${data.user_id})`);
                }
            } catch (err) {
                console.error("❌ JSON Parse Error:", err.message);
            }
        });
        console.log("✅ Subscribed to 'message' channel");
    } catch (err) {
        console.error("🚨 Subscription Error:", err.message);
    }

    // Clients send a signed JWT after connecting so we can route per-user
    // notifications to the right room. The token must be issued by the
    // Laravel backend (tymon/jwt-auth) using the same JWT_SECRET — never
    // trust a client-supplied user id.
    socket.on('authenticate', (token) => {
        const secret = process.env.JWT_SECRET;
        if (!secret) {
            console.error('🚨 JWT_SECRET not configured; refusing socket auth.');
            socket.emit('auth_error', 'Server auth misconfigured');
            return;
        }
        if (!token || typeof token !== 'string') {
            socket.emit('auth_error', 'Missing token');
            return;
        }
        try {
            const payload = jwt.verify(token, secret);
            // tymon/jwt-auth puts the user id in the `sub` claim.
            const userId = payload?.sub ?? payload?.user_id;
            if (!userId) {
                socket.emit('auth_error', 'Token missing subject');
                return;
            }
            const room = `user-${userId}`;
            socket.join(room);
            console.log(`🔐 Socket ${socket.id} authenticated as ${userId} → ${room}`);
        } catch (err) {
            console.error(`🚨 Auth failed for ${socket.id}:`, err.message);
            socket.emit('auth_error', 'Invalid or expired token');
        }
    });

    // Cleanup Redis subscriber on client disconnect.
    // Guard against ClientClosedError (subscriber already closed after a
    // prior Redis error) — propagating it would land in the global
    // unhandledRejection handler and `process.exit(1)` the entire server.
    socket.on('disconnect', async () => {
        console.log("❌ Client disconnected:", socket.id);
        try {
            if (subscriber.isOpen) {
                await subscriber.quit();
            }
            console.log("🔌 Redis Subscriber Disconnected");
        } catch (err) {
            console.error("⚠️ Error closing Redis subscriber:", err.message);
        }
    });

    // Socket error handling
    socket.on('error', (err) => console.error("🚨 Socket Error:", err));

    // Heartbeat check for dead connections
    socket.on('ping', () => socket.emit('pong'));
});

// Global error handling for unexpected errors
process.on('uncaughtException', (err) => {
    console.error("🔥 Uncaught Exception:", err);
    process.exit(1);
});
process.on('unhandledRejection', (err) => {
    console.error("🔥 Unhandled Rejection:", err);
    process.exit(1);
});
