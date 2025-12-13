import dotenv from 'dotenv';
import fs from 'fs';
import path from 'path';
import express from 'express';
import redis from 'redis';
import http from 'http';
import https from 'https';
import { Server as SocketIo } from 'socket.io';

// Load environment variables from the parent directory .env file
// dotenv.config({ path: path.join(__dirname, '../.env') });
dotenv.config({ path: path.join('../.env') });

const PORT = process.env.PORT_NOTIFICATIONS || 8890;
const HTTPS_ENABLED = process.env.HTTPS === 'TRUE';

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
            ca: fs.readFileSync(process.env.SSL_CA, 'utf8')
        };
        server = https.createServer(options, app);
    } catch (error) {
        console.error("❌ SSL Error:", error.message);
        process.exit(1);
    }
} else {
    server = http.createServer(app);
}

// Initialize Socket.io with proper CORS settings
const io = new SocketIo(server, {
    cors: {
        origin: process.env.APP_URL || "*",
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
        server.close(() => {
            console.log("Server closed.");
            process.exit(0);
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

    // ✅ Each socket gets its own Redis subscriber for isolating message handling
    const subscriber = redis.createClient(redisConfig);
    subscriber.on('error', (err) => console.error("🚨 Redis Subscriber Error:", err));

    await subscriber.connect();
    console.log("✅ Redis Subscriber Connected for", socket.id);

    try {
        await subscriber.subscribe('message', (message, channel) => {
            console.log(`📩 Received message on ${channel}:`, message);

            try {
                const data = JSON.parse(message);
                if (data.channel === 'notifications') {
                    // Send message to specific event for a user
                    const eventName = `${channel}-notifications-${data.user_id}`;
                    io.emit(eventName, data.data);
                    console.log(`📩 Sent message to ${eventName}`);
                }
            } catch (err) {
                console.error("❌ JSON Parse Error:", err.message);
            }
        });
        console.log("✅ Subscribed to 'message' channel");
    } catch (err) {
        console.error("🚨 Subscription Error:", err.message);
    }

    // Cleanup Redis subscriber on client disconnect
    socket.on('disconnect', async () => {
        console.log("❌ Client disconnected:", socket.id);
        await subscriber.quit();
        console.log("🔌 Redis Subscriber Disconnected");
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
