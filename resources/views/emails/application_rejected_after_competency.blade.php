@component('mail::message')
    <div class="header">
        Dear Respected Candidate,
    </div>
    <div class="content">
        <p>
            Thank you for participating in the competency exam as part of the selection process for the Paramedic position.
        </p>

        <p>
            After reviewing your exam results, we regret to inform you that you did not meet the passing criteria for the competency exam to proceed further in the recruitment process. As such, we will not be moving forward with your application at this time.
             </p>

        <p>
            We appreciate the time and effort you dedicated to this process and encourage you to continue developing your skills and knowledge. We wish you all the best in your future professional endeavors.

        </p>

        <p>Thanks,<br></p>
    </div>


    <br>
    {{ config('app.name') }}
@endcomponent
