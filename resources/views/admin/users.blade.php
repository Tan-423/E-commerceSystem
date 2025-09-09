@extends('layouts.admin')
@section('content')

    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>All Registered Users</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Users</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <div class="text-tiny">Total Users: {{ $users->total() }}</div>
                    </div>
                </div>
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        @if(Session::has('status'))
                            <p class="alert alert-success">{{ Session::get('status') }}</p>
                        @endif
                        @if(Session::has('error'))
                            <p class="alert alert-danger">{{ Session::get('error') }}</p>
                        @endif
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>User Type</th>
                                    <th>Status</th>
                                    <th>Failed Attempts</th>
                                    <th>Registered Date</th>
                                    <th>Last Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            <div class="body-title">{{ $user->name }}</div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->usertype == 'ADM')
                                                <span class="badge bg-success">Admin</span>
                                            @else
                                                <span class="badge bg-primary">Customer</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->locked)
                                                <span class="badge bg-danger">Locked</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->failed_attempts > 0)
                                                <span class="badge bg-warning">{{ $user->failed_attempts }}/3</span>
                                                @if($user->last_failed_attempt)
                                                    <br><small class="text-muted">{{ $user->last_failed_attempt->format('M d, Y H:i') }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">0/3</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($user->usertype != 'ADM')
                                                @if($user->locked)
                                                    <form method="POST" action="{{ route('admin.user.unlock', $user->id) }}" style="display: inline;" 
                                                          onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<i class=\"icon-hourglass\"></i> 
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success" 
                                                                onclick="return confirm('Are you sure you want to unlock this user?')">
                                                            <i class="icon-unlock"></i> Unlock
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.user.lock', $user->id) }}" style="display: inline;" 
                                                          onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<i class=\"icon-hourglass\"></i>
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-warning" 
                                                                onclick="return confirm('Are you sure you want to lock this user?')">
                                                            <i class="icon-lock"></i> Lock
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="text-muted">Admin</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
