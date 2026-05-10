@props(['title', 'value', 'icon', 'color' => 'primary'])

<div class="col-md-3 col-sm-6 mb-4">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
                <div class="rounded-circle bg-{{ $color }} bg-opacity-10 p-3">
                    <i class="fas fa-{{ $icon }} fa-lg text-{{ $color }}"></i>
                </div>
            </div>
            <div>
                <div class="text-muted small">{{ $title }}</div>
                <div class="fs-4 fw-bold">{{ $value }}</div>
            </div>
        </div>
    </div>
</div>
