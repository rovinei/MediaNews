@if(!empty($category))
<div class="uk-panel-body no-pad-right no-pad-bottom">
    <figure class="uk-overlay uk-overlay-hover">
        <div class="post-grid__categoy">
            <h3 class="font-kh-nokora">@if($category){{ $category->name }}@endif</h3>
        </div>
        <img class="uk-overlay-scale" src="@if($category->latestVideo->featured_image){{ asset($category->latestVideo->featured_image) }}@else{{ asset('images/no_thumbnail_img.jpg') }}@endif" alt="">
        <figcaption class="font-kh-hanuman uk-overlay-background uk-ignore uk-overlay-panel uk-flex uk-flex-bottom">
            <div class="inner">
                <p>
                    {{ str_limit($category->latestVideo->title, 70) }}
                </p>
                <div class="datetime">
                    <i class="fa fa-clock-o"></i>
                    {{ $category->latestVideo->created_at->format('d\\, M\\, Y') }}
                </div>
            </div>
        </figcaption>
        <a href="{{ route('visitor.video.detail', $category->latestVideo->id) }}" class="uk-position-cover"></a>
    </figure>
</div>
@endif
