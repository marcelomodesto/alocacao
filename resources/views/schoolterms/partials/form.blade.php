<div class="row custom-form-group align-items-center">
    <div class="col-12 col-lg-5 text-lg-right">
        <label for="year">Ano *</label>
    </div>
    <div class="col-12 col-md-2">
        <input class="custom-form-control" type="text" name="year" id="year"
            value='{{ $periodo->year ?? ""}}'
        />
    </div>
</div>

<div class="row custom-form-group align-items-center">
    <div class="col-12 col-lg-5 text-lg-right">
        <label for="period">Período *</label>
    </div>
    <div class="col-12 col-md-2">
        <select class="custom-form-control" type="text" name="period"
            id="period"
        >
            <option value="" {{ ( $periodo->period) ? '' : 'selected'}}></option>

            @foreach ([
                        '1° Semestre',
                        '2° Semestre',
                     ] as $period)
                <option value="{{ $period }}" {{ ( $periodo->period === $period) ? 'selected' : ''}}>{{ $period }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-4 d-none d-lg-block"></div>
    <div class="col-md-12 col-lg-6">
        <button type="submit" class="btn btn-outline-dark">
            {{ $buttonText }}
        </button>
        <a class="btn btn-outline-dark"
            href="{{ route('schoolterms.index') }}"
        >
            Cancelar
        </a>
    </div>
</div>