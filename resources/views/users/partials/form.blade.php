<div class="row custom-form-group align-items-center">
    <div class="col-12 col-lg-4 text-lg-right">
        <label for="nome">Nome *</label>
    </div>
    <div class="col-12 col-md-5">
        <input class="custom-form-control" type="text" name="name" id="name"
            value='{{ $user->name ?? ""}}'
        />
    </div>
</div>

@error('name')
    <div class="row mb-2">
        <div class="col-4 d-none d-lg-block"></div>
        <div class="col-12 col-md-5">
            <div class="alert alert-danger rounded-0">
                * Campo obrigatório.
            </div>
        </div>
    </div>
@enderror

<div class="row custom-form-group align-items-center">
    <div class="col-12 col-lg-4 text-lg-right">
        <label for="email">E-mail *</label>
    </div>
    <div class="col-12 col-md-5">
        <input class="custom-form-control" type="text" name="email"
            id="email" value='{{ $user->email ?? ""}}'
        />
    </div>
</div>


@error('email')
    <div class="row mb-2">
        <div class="col-4 d-none d-lg-block"></div>
        <div class="col-12 col-md-5">
            <div class="alert alert-danger rounded-0">
                * Campo obrigatório.
            </div>
        </div>
    </div>
@enderror

<div class="row custom-form-group align-items-center">
    <div class="col-12 col-lg-4 text-lg-right">
        <label for="role">Perfis *</label>
    </div>
    <div class="col-12 col-md-5">
        @foreach ($roles as $role)
            <div>
                <input class="checkbox" type="checkbox" name="roles[]" id="check-box-{{$role->id}}" 
                value="{{ $role->name }}" {{ $user->roles->contains("name", $role->name) ? "checked" : "" }} />
                <label for="check-box-{{$role->id}}">{{$role->name}}</label>
            </div>
        @endforeach 
    </div>
</div>


@error('roles')
    <div class="row mb-2">
        <div class="col-4 d-none d-lg-block"></div>
        <div class="col-12 col-md-5">
            <div class="alert alert-danger rounded-0">
                * Campo obrigatório.
            </div>
        </div>
    </div>
@enderror

<div class="row">
    <div class="col-4 d-none d-lg-block"></div>
    <div class="col-md-12 col-lg-6">
        <button type="submit" class="btn btn-outline-dark">
            {{ $buttonText }}
        </button>
        <a class="btn btn-outline-dark"
            href="{{ route('users.index') }}"
        >
            Cancelar
        </a>
    </div>
</div>