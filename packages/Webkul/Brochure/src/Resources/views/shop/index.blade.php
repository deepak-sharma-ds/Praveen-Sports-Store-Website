{{--
    Legacy stub — the shop controller now renders brochure::shop.brochure.index directly.
    This file is kept to avoid any cached view reference errors.
--}}
@php
    // Hard redirect in case this view is ever loaded directly
    abort(redirect()->route('shop.brochure.index'));
@endphp
