@if(!empty($video))
<!-- Top video slideshow -->
<div class="video-slideshow bg-dark-grey">
    <div class="uk-container uk-container-center">
        <div class="video-slideshow__outter uk-clearfix">

            <div class="video-preview">
                @if($video->video_url)
                <iframe width="100%" height="430" src="https://youtube.com/embed/{{ $video->video_url }}" frameborder="0" allowfullscreen></iframe>
                @endif
            </div>

            <div class="video-desc">
                <div class="inner uk-clearfix">
                    <div class="breadcrum uk-margin-top">
                        <h3 class="font-kh-nokora uk-margin-remove yellow">
                            <i class="fa fa-play"></i>
                            <a href="{{ route('visitor.video.page') }}">@lang('visitor.video')</a>
                            @if($video->category != null)
                            <i class="fa fa-angle-double-right"></i>
                            <a href="{{ route('visitor.video.category', $video->category->id) }}">
                                {{ $video->category->name }}
                            </a>
                            @endif
                        </h3>
                    </div>

                    <h3 class="title font-yellow">
                        {{ $video->title }}
                    </h3>

                    <div class="datetime">
                        <i class="fa fa-clock-o"></i>
                        {{ $video->created_at->diffForHumans() }}
                    </div>

                    <div class="snippet">
                        <p>
                        @if($video->content)
                            {!! str_limit(strip_tags($video->content), 150) !!}
                        @endif
                        </p>
                    </div>

                    <div class="socials">
                        <div class="social-share">

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- /Top video slideshow -->
@endif
