@include('component.header.styles')

@include('component.header.modals.cashier-start')
@include('component.header.modals.day-end')
@include('component.header.navbar')


<div class="leftside-menu">

    @php
    $company = user_data('company') ?? null;
    $logo = $company['Logo'] ?? null;
    @endphp


    <!-- Brand Logo Light -->
    <a href="/" class="logo logo-light">
        @if ($logo)
        <img src="{{ $logo }}" class="logo-img rounded-logo">
        @else
        <img src="https://accountcenter.asipbook.com/asipiya.svg" class="logo-img rounded-logo">
        @endif
    </a>

    <!-- Brand Logo Dark -->
    <a href="/" class="logo logo-dark">
        @if ($logo)
        <img src="{{ $logo }}" class="logo-img rounded-logo">
        @else
        <img src="https://accountcenter.asipbook.com/asipiya.svg" class="logo-img rounded-logo">
        @endif
    </a>



    @include('layout.sidebar')

</div>

@include('component.header.scripts')