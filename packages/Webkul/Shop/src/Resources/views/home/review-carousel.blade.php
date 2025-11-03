@php
    use Webkul\Product\Models\ProductReview;

    $reviews = ProductReview::with('product')->where('status', 1)->latest()->take(10)->get();
@endphp

@if ($reviews->count())
    <section class="customer-review-section py-10 bg-gray-50">
        <div id="reviewCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                @foreach ($reviews as $index => $review)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                        <div class="text-center">
                            <h5 class="fw-bold">{{ strtoupper($review->title) }}</h5>
                            <p class="text-muted">{{ $review->comment }}</p>
                            <p>⭐ {{ $review->rating }}/5 by <strong>{{ $review->name }}</strong></p>
                            <p><small>on {{ $review->product?->name }}</small></p>
                        </div>
                    </div>
                @endforeach

            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#reviewCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#reviewCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>
@endif
