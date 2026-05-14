<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{{ $user->name }}</p>
</div>

<!-- Content Field -->
<div class="form-group">
    {!! Form::label('email', 'Email:') !!}
    <p>{{ $user->email }}</p>
</div>

<!-- Purpose Field -->
<div class="form-group">
    {!! Form::label('mobile', 'Mobile:') !!}
    <p>{{ $user->mobile }}</p>
</div>

<!-- From Field -->
<div class="form-group">
    {!! Form::label('freeze', 'Freeze:') !!}
    <p>{{ $user->freeze == 1 ? 'Frozen' : 'Active' }}</p>
</div>
