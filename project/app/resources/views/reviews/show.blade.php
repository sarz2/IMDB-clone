<!DOCTYPE html>
<html lang="en">

    @include('_head')
    
<body class="bg-gray-300 font-sans antialiased">
    <div class="container block mx-auto px-2 py-4">
    @include('_nav')


    <div>
        <div class="mb-2 shadow-lg rounded-t-8xl rounded-b-5xl overflow-hidden">
            <div class="pt-3 pb-3 md:pb-1 px-4 md:px-16 bg-white bg-opacity-40">
                <div class="flex flex-wrap items-center">
                    <h4 class="w-full md:w-auto text-xl font-heading font-medium">{{ $review->user->name }} </h4>
                    <div class="w-full md:w-px h-2 md:h-8 mx-8 bg-transparent md:bg-gray-200"></div>
                    <div class="inline-flex">
                        @for ($i = 0; $i < $review->review_rating; $i++)
                            <a class="inline-block mr-1" href="#">
                                <svg width="20" height="20" viewbox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20 7.91677H12.4167L10 0.416763L7.58333 7.91677H0L6.18335 12.3168L3.81668 19.5834L10 15.0834L16.1834 19.5834L13.8167 12.3168L20 7.91677Z"
                                        fill="#FFCB00"></path>
                                </svg>
                            </a>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="px-4 overflow-hidden md:px-16 pt-8 pb-12 bg-white">
                <div class="flex flex-wrap">
                    <div class="w-full md:w-2/3 mb-6 md:mb-0">
                        <h4 class="w-full md:w-auto text-l font-heading font-medium">{{ $review->title }} </h4>
                        <p class="mb-8 max-w-2xl text-darkBlueGray-400 leading-loose">{{ $review->review_content }}
                        </p>
                    </div>
                    <div class="w-full md:w-1/3 text-right">
                        <?php $date = date_create($review['created_at']); ?>
                        <p class="mb-8 text-sm text-gray-600">Created at {{ date_format($date, 'Y-m-d') }}</p>
                    </div>
                </div>
                <a class=" mx-auto mt-16 text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                    href="{{ url()->previous() }}"> Go back</a>
            </div>
        </div>
        <br>
    </div>
    </div>
</body>

</html>
