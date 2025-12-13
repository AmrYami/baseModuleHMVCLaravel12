<x-auth-layout>
    <div class="d-flex flex-column flex-center w-100 min-vh-100 bg-light">
        <!--begin::Wrapper-->
        <div class="text-center p-10">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder fs-1 mb-5">Welcome to the Workforce Application Portal – Fakeeh Care Group</h1>
            <p>
                We’re delighted that you’re interested in joining our team for this program. Your commitment and skills play a vital role in serving this important mission. Please fill out the form below to begin your application. We look forward to working with dedicated individuals like you to ensure a successful and meaningful season.
            </p>
            <!--end::Title-->

            <!--begin::Buttons-->
            <div class="d-flex flex-center flex-wrap gap-5">
                <a href="{{ route('login') }}" class="btn btn-primary px-8 py-3">Login</a>
{{--                <a href="{{ route('register') }}" class="btn btn-outline-primary px-8 py-3">Register</a>--}}
            </div>
            <!--end::Buttons-->
        </div>
        <!--end::Wrapper-->
    </div>
</x-auth-layout>
