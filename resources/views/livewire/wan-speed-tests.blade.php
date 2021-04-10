<div wire:poll.10s>

    <header>
        <h2>WAN speed tests <small>Past 24hours</small></h2>
    </header>

    <div wire:ignore>
        <div  wire:key={{ $chart_id }}>
            @if($chart)
                {!! $chart->container() !!}
            @endif
        </div>
    </div>

</div>

@if($chart)
    @push('scripts')
    
        {!! $chart->script() !!}
    @endpush
@endif


@push('pack')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.min.js"></script>

@endpush