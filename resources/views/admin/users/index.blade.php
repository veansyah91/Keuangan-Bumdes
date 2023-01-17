@extends('layouts.admin')

@section('admin')
    <div class="page-heading">
        <h3>Pengguna  @if (Auth::user()->getRoleNames()[0] == 'DEV') Admin @endif</h3>
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-12 col-md-12">
                @role('DEV')
                    @if ($users->count() < 2) 
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" >
                            Tambah Pengguna
                        </button>
                    @endif
                @endrole
    
                @role('ADMIN')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="addUser()">
                        Tambah Pengguna
                    </button>
                @endrole
                
                @if (session('success'))
                    <div class=" mt-3 alert alert-success d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                        <div>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                
                <table class="table mt-3 table-responsive">
                    <thead>
                        <tr>
                            <th>Nama User</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Unit Usaha</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($users->isNotEmpty())
                                @if (Auth::user()->getRoleNames()[0] == 'DEV')
                                    @foreach ($users as $user)
                                        @if ($user->getRoleNames()[0] == 'ADMIN')
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" onclick="resetPassword({{ $user->id }})">Reset Password</button>
                                                </td>
                                                <td>{{ $user->getRoleNames()[0] }}</td>
                                                <td>
                                                    @if ($user->businesses->count() > 0)
                                                        @foreach ($user->businesses as $business)
                                                            {{ $business->nama }}
                                                        @endforeach
                                                    @else
                                                        -
                                                    @endif
                                                <td>
                                                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteConfirmation({{ $user->id }})">Hapus</button>
                                                </td>
                                            </tr>   
                                        @endif
                                    @endforeach
                                @else
                                    @foreach ($users as $user)
                                        @if ($user->getRoleNames()[0] !== 'DEV')
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" onclick="resetPassword({{ $user->id }})">Reset Password</button>
                                                </td>
                                                <td>{{ $user->getRoleNames()[0] }}</td>
                                                <td>
                                                    @php
                                                        $usaha = '';
                                                    @endphp
                                                    @if ($user->businesses->count() > 0)
                                                        {{ $user->businesses[0]->nama }}
                                                        @php
                                                            $usaha = $user->businesses[0]->id
                                                        @endphp
                                                    @else
                                                        -
                                                    @endif
                                                <td>
                                                    <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#editRoleModal" onclick="editRoleFunc({{ $user->id }}, '{{ $usaha }}', '{{ $user->getRoleNames()[0] }}')">Edit</button>
                                                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteConfirmation({{ $user->id }})">Hapus</button>
                                                </td>
                                            </tr>                                        
                                        @endif 
                                    @endforeach
                                @endif
                        @else
                            <tr>
                                <td colspan="2">
                                    <center> <i>Data Belum Ada</i> </center>
                                </td>
                            </tr>
                        @endif
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--Input Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="userModalLabel">Tambah Pengguna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input type="text" class="form-control" id="password" name="password" aria-describedby="passwordHelp" value="indonesia123" required>
                            </div>

                            @role('ADMIN')
                                <div class="mb-3">
                                    <label for="role" class="form-label fw-bold">Role</label>
                                    <select class="form-select role select-role" aria-label="roleSelect" name="role">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 d-none business-section">
                                    <label for="usaha" class="form-label fw-bold">Unit Usaha</label>
                                    <select class="form-select usaha" aria-label="usahaSelect" name="business" @if ($businesses->isEmpty()) disabled @endif>
                                        @if ($businesses->isEmpty())
                                            <option selected>Belum Ada Unit Usaha</option>
                                        @else 
                                            <option value=0 selected>-- Pilih Unit Usaha --</option>
                                            @foreach ($businesses as $business)
                                                <option value="{{ $business->id }}">{{ $business->nama }}</option>
                                            @endforeach
                                        @endif
                                        
                                    </select>
                                </div>
                            @endrole
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="submit-button">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form method="POST" id="reset-password-form">
                @csrf
                @method('patch')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">Reset Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input type="text" class="form-control" id="password" name="password" aria-describedby="passwordHelp" value="indonesia123" required>
                            </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="submit-button">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form method="POST" id="edit-role-form">
                @csrf
                @method('patch')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="editRoleModalLabel">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        @role('ADMIN')
                            <div class="mb-3">
                                <label for="role" class="form-label fw-bold">Role</label>
                                <select class="form-select role select-role" aria-label="roleSelect" name="role">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                    
                                </select>
                            </div>

                            <div class="mb-3 d-none business-section">
                                <label for="usaha" class="form-label fw-bold">Unit Usaha</label>
                                <select class="form-select usaha" aria-label="usahaSelect" name="business" @if ($businesses->isEmpty()) disabled @endif>
                                    @if ($businesses->isEmpty())
                                        <option selected>Belum Ada Unit Usaha</option>
                                    @else 
                                        <option value=0 selected>-- Pilih Unit Usaha --</option>
                                        @foreach ($businesses as $business)
                                            <option value="{{ $business->id }}">{{ $business->nama }}</option>
                                        @endforeach
                                    @endif
                                    
                                </select>
                            </div>
                    @endrole
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="submit-button">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form method="POST" id="deleteForm">
                @csrf
                @method('delete')
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title text-center" id="deleteModalLabel">Anda Yakin Hapus Data Ini?</h3>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="submit-delete-button">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
        
@endsection

@section('script')
    <script type="text/javascript">
        const usaha = Array.from(document.getElementsByClassName('usaha'));
        const role = Array.from(document.getElementsByClassName('role'));

        const businessSection = document.getElementsByClassName('business-section');
        const selectRoles = Array.from(document.getElementsByClassName('select-role'));

        const deleteConfirmation = (id) => {
            const deleteForm = document.getElementById('deleteForm');

            deleteForm.setAttribute('action',`/users/${id}`);
        }

        const resetPassword = (id) => {
            const resetPasswordForm = document.getElementById('reset-password-form');

            resetPasswordForm.setAttribute('action',`/users/reset-password/${id}`)
        }

        const addUser = () => {
            if (role[0].value == "ADMIN") {
                businessSection[0].classList.remove('d-block');
                businessSection[0].classList.add('d-none');
                usaha[0].value = 0;
            } else {
                businessSection[0].classList.add('d-block');
                businessSection[0].classList.remove('d-none');
            }
        }

        const editRoleFunc = (id, usahaUser, roleUser) => {
            const resetRoleForm = document.getElementById('edit-role-form');
            
            resetRoleForm.setAttribute('action',`/users/reset-role/${id}`)
            role[1].value = roleUser;


            if (role[1].value == "ADMIN") {
                businessSection[1].classList.remove('d-block');
                businessSection[1].classList.add('d-none');
                
            } else {
                businessSection[1].classList.add('d-block');
                businessSection[1].classList.remove('d-none');
                usaha[1].value = usahaUser;
            }
        }

        selectRoles.map((selectRole, index) => {
            selectRole.addEventListener('change', function(){
                if (selectRole.value == "ADMIN") {
                    businessSection[index].classList.remove('d-block');
                    businessSection[index].classList.add('d-none');
                    usaha[0].value = 0;
                } else {
                    businessSection[index].classList.add('d-block');
                    businessSection[index].classList.remove('d-none');
                }
            })
        })

        window.addEventListener('load', function (){
            
        })
    </script>
@endsection
