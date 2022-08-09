
<div class="row custom-form-group align-items-center">
    <div class="col-12 col-lg-4 text-lg-right">
        <label for="title">Título*</label>
    </div>
    <div class="col-12 col-md-5">
        <input class="custom-form-control" type="text" name="title" id="title"
            value="{{ old('title') ?? $observation->title ?? ''}}" 
        />
    </div>
</div>

<div class="row custom-form-group align-items-center">
    <div class="col-12 col-lg-4 text-lg-right">
        <label for="body">Corpo*</label>
    </div>
    <div class="col-12 col-md-5">
        <textarea class="custom-form-control" type="text" name="body" id="body" style="height: 200px;"/>
            {{ old('body') ?? $observation->body ?? ''}}
        </textarea>
    </div>
</div>

<div class="row">
    <div class="col-4 d-none d-lg-block"></div>
    <div class="col-md-12 col-lg-6">
        <button type="submit" class="btn btn-outline-dark">
            {{ $buttonText }}
        </button>
        <a class="btn btn-outline-dark"
            href="{{ route('observations.index') }}"
        >
            Cancelar
        </a>
    </div>
</div>