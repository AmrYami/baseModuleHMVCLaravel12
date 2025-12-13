<x-layout.mt.sidebar.item :title="__('Dashboard')" :url="route('dashboard')">
    <x-slot:icon>
        <i class="ki-duotone ki-element-11 fs-2">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
            <span class="path4"></span>
        </i>
    </x-slot:icon>

</x-layout.mt.sidebar.item>
@canany(['list-users','create-users'])
    <x-layout.mt.sidebar.parent-item :mainTitle="__('Users')">
        <x-slot:mainIcon>
            <i class="ki-duotone ki-people fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
                <span class="path5"></span>
            </i>
        </x-slot:mainIcon>
        <x-slot:submenu>
            @can('create-users')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.users.create')" :title="__('sidebar.Create')"/>
            @endcan
            @can('list-users')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.users.index')" :title="__('sidebar.Users')"/>
            @endcan
        </x-slot:submenu>
    </x-layout.mt.sidebar.parent-item>
@endcanany



@canany(['list-users-role','create-users-role'])
    <x-layout.mt.sidebar.parent-item :mainTitle="__('Roles')">
        <x-slot:mainIcon>
            <i class="ki-duotone ki-user-tick fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </x-slot:mainIcon>
        <x-slot:submenu>
            @can('create-users-role')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.roles.create')" :title="__('sidebar.Create')"/>
            @endcan
            @can('list-users-role')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.roles.index')" :title="__('sidebar.Roles')"/>
            @endcan
        </x-slot:submenu>
    </x-layout.mt.sidebar.parent-item>
@endcanany

@can('users-logs')
    <x-layout.mt.sidebar.item :title="__('Activity Logs')" :url="route('dashboard.logs.index')">
        <x-slot:icon>
            <i class="ki-duotone ki-time fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </x-slot:icon>
    </x-layout.mt.sidebar.item>
@endcan

@if(config('assessments.enabled') && config('assessments.admin_only') && class_exists(\Fakeeh\Assessments\AssessmentsServiceProvider::class))
@canany(['exams.topics.index','exams.questions.index','exams.exams.index','exams.reports.index'])
    <x-layout.mt.sidebar.parent-item :mainTitle="__('Assessments')">
        <x-slot:mainIcon>
            <i class="ki-duotone ki-exam fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </x-slot:mainIcon>
        <x-slot:submenu>
            @can('exams.topics.index')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.assessments.topics.index')" :title="__('Topics')"/>
            @endcan
            @can('exams.questions.index')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.assessments.questions.index')" :title="__('Questions')"/>
            @endcan
            @can('exams.exams.index')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.assessments.exams.index')" :title="__('Exams')"/>
            @endcan
            @can('exams.reviews.index')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.assessments.reviews.index')" :title="__('Reviews')"/>
            @endcan
            @can('exams.answersets.index')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.assessments.answer_sets.index')" :title="__('Answer Sets')"/>
            @endcan
            @can('exams.reports.index')
                <x-layout.mt.sidebar.sub-item :url="'#'" :title="__('Reports (coming soon)')"/>
            @endcan
        </x-slot:submenu>
    </x-layout.mt.sidebar.parent-item>
@endcanany
@endif

@if(config('assessments.enabled') && !config('assessments.admin_only'))
@can('exams.attempts.start')
    <x-layout.mt.sidebar.item :title="__('Exams')" :url="route('assessments.candidate.exams.index')">
        <x-slot:icon>
            <i class="ki-duotone ki-exam fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </x-slot:icon>
    </x-layout.mt.sidebar.item>
@endcan
@endif


@canany(['setting-notifications'])
    <x-layout.mt.sidebar.parent-item :mainTitle="__('sidebar.Setting')">
        <x-slot:mainIcon>
            <i class="fa-solid fa-gears fs-2"></i>
            {{--  <i class="ki-duotone ki-user-tick fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>  --}}
        </x-slot:mainIcon>
        <x-slot:submenu>
            @can('setting-notifications')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.setting.edit', ['key' => 'notifications'])"
                                              :title="__('sidebar.Notifications')"/>
            @endcan
            @can('setting-notifications')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.setting.edit', ['key' => 'emails'])"
                                              :title="__('Emails')"/>
            @endcan
            @can('setting-notifications')
                <x-layout.mt.sidebar.sub-item :url="route('dashboard.setting.edit', ['key' => 'registration'])"
                                              :title="__('Registration')"/>
            @endcan
        </x-slot:submenu>
    </x-layout.mt.sidebar.parent-item>

@endcanany
