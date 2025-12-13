<!--begin: Datatable-->
<table class="table table-separate table-head-custom" id="kt_datatable">
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        {{--                                    <th>Type</th>--}}
        <th>Role</th>
        @canany(['edit-users','block-users', 'delete-users'])
            <th>{{trans("modules.Action")}}</th>
        @endcanany
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->mobile }}</td>
            {{--                    <td>{{ $user->type }}</td>--}}
            {{--                        <td>{{ ucfirst($user->type) }}</td>--}}
            <td>{{ implode(',', $user->roles->pluck('name')->toArray()) }}</td>
            @canany(['edit-users','block-users', 'delete-users'])
                {{--                            @if($flag ?? '')--}}
                {{--                                <td>--}}
                {{--                                    <div class='btn-group'>--}}
                {{--                                        @can('edit-users')--}}
                {{--                                            <a href="{{ route('dashboard.users.restore', [$user->id]) }}"--}}
                {{--                                               class='btn btn-default btn-xs'><i class="fas fa-trash-restore"></i></a>--}}
                {{--                                        @endcan--}}
                {{--                                    </div>--}}
                {{--                                </td>--}}
                {{--                            @endif--}}
                <td>
                    @if($user->id != 1)
                    <div class='btn-group'>
                        @can('edit-users')
                            <a href="{{ route('dashboard.users.edit', [$user->hashid ?? hid($user->id)]) }}"
                               class='btn btn-default btn-xs'><i class="fa fa-edit"></i></a>
                        @endcan
                        @can("delete-users")
                                {!! html()->form('DELETE', route('dashboard.users.destroy', $user->hashid ?? hid($user->id)))
                                    ->class('d-inline-block')
                                    ->open()
                                !!}

                                {!! html()->button('<i class="fa fa-trash"></i>')
                                    ->type('submit')
                                    ->class('btn btn-danger btn-xs')
                                    ->attribute('onclick', "return confirm('Are you sure you want to delete this user?')") !!}

                                {!! html()->closeModelForm() !!}

                                @php $isBanned = isset($user->banned_until) && now()->lt($user->banned_until); @endphp
                                @if(!$user->freeze && !$isBanned)
                                    <a href="{{ route('dashboard.users.freeze.form', $user->hashid ?? hid($user->id)) }}" class="btn btn-danger btn-xs">Freeze</a>
                                @else
                                    {!! html()->form('PUT', route('dashboard.users.un_freeze', $user->hashid ?? hid($user->id)))
                                        ->class('d-inline-block')
                                        ->open() !!}

                                    {!! html()->button('Un-Freeze')
                                        ->type('submit')
                                        ->class('btn btn-danger btn-xs')
                                        ->attribute('onclick', "return confirm('Are you sure you want to un-freeze this user?')") !!}

                                    {!! html()->closeModelForm() !!}
                                @endif

                                {{--                                    {!! Form::open(['route' => ['users.banned_until', $user->id], 'method'--}}
                            {{--                                    => 'PUT', 'class' =>'d-inline-block']) !!}--}}
                            {{--                                    {!! Form::button('Banned Until',--}}
                            {{--                                        ['type' => 'submit', 'class' => 'btn btn-danger btn-xs',--}}
                            {{--                                        'onclick' => "return confirm('Are you sure you woant to freeze this user?')"]) !!}--}}
                            {{--                                    {!! Form::closeModelForm() !!}--}}
                        @endcan
                    </div>
                    @endif
                </td>
            @endcanany
        </tr>
    @endforeach
    </tbody>
</table>
<!--end: Datatable-->
<!--end::Card-->
