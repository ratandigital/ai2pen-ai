@extends('layouts.landing')
@section('title',$meta_title)
@section('meta_title',$meta_title)
@section('meta_description',$meta_description)
@section('meta_keyword',$meta_keyword)
@section('meta_author',$meta_author)
@section('meta_image',$meta_image)
@section('meta_image_width',$meta_image_width)
@section('meta_image_height',$meta_image_height)
@section('content')

  <!-- ===== Hero Section Start ===== -->
  @include('landing.partials.hero')
  <!-- ===== Hero Section End ===== -->

  <!-- ===== Features Section Start ===== -->
  <section id="features" class="pt-14 sm:pt-20 lg:pt-[130px]">
    <div class="px-4 xl:container">
      <!-- Section Title -->
      <div class="relative mx-auto mb-12 max-w-[620px] pt-6 text-center md:mb-20 lg:pt-16" data-wow-delay=".2s">
        <span class="title"> {{__("FEATURES")}} </span>
        <h2 class="mb-5 font-heading text-3xl font-semibold text-dark dark:text-white sm:text-4xl md:text-[50px] md:leading-[60px]"> {{__("Unique & Awesome Core Features")}}</h2>
        <p class="text-base text-dark-text">{{__("Everything you will need to boost your work is right at your fingertips. Take a look at what you can do and how to get started.")}}</p>
      </div>
      <div class="-mx-4 flex flex-wrap justify-center">
        @foreach($ai_sidebar_group_by_id as $menu_group_id=>$menu_items)
          <?php if(empty($menu_items)) continue; ?>
          @foreach($menu_items as $menu_item)
              <?php
                  $action_url = route('dashboard');
                  $has_access = $menu_item['has_access'] ?? true;
                  $about_text = !empty($menu_item['about_text']) ? $menu_item['about_text'].' : ' : '';
              ?>
              <div class="w-full px-4 md:w-1/2 lg:w-1/3 cs-icon-lg-container">
                <div class="group mx-auto mb-10 max-w-[380px] text-center md:mb-16" data-wow-delay=".2s">
                  <div class="mx-auto mb-6 flex h-[70px] w-[70px] items-center justify-center rounded-full bg-primary bg-opacity-5 text-primary transition group-hover:bg-primary group-hover:bg-opacity-100 group-hover:text-white dark:bg-white dark:bg-opacity-5 dark:text-white dark:group-hover:bg-primary dark:group-hover:bg-opacity-100 md:mb-9 md:h-[90px] md:w-[90px]">
                    <i class="{{$menu_item['template_thumb']}} cs-icon-lg"></i>
                  </div>
                  <div>
                    <h3 class="mb-3 font-heading text-xl font-medium text-dark dark:text-white sm:text-2xl md:mb-5"> {{__($menu_item['template_name'])}} </h3>
                    <p class="text-base text-dark-text">{{__($menu_item['template_description'])}}</p>
                  </div>
                </div>
              </div>
          @endforeach
        @endforeach
      </div>
    </div>
  </section>
  <!-- ===== Features Section End ===== -->


  <!-- ===== About Section Start ===== -->
  <?php
    $use_cases = [
      ['title'=>__("Marketers"),'description'=>__("Can be used by marketers to generate high-quality and engaging content for their social media channels, blogs, and websites. These tools can help save time and resources while ensuring that the content is optimized for SEO and tailored to the brand's voice and tone.")],
      ['title'=>__("Copywriters"),'description'=>__("Copywriters can use content generation tools to generate new ideas, headlines, and marketing copy. These tools can help copywriters speed up the ideation and brainstorming process, freeing up more time for them to focus on the actual writing and creative aspects of their job.")],
      ['title'=>__("Journalists"),'description'=>__("Can be used by journalists to generate news articles on various topics. These tools can help journalists speed up the research process and produce articles faster, allowing them to stay ahead of the competition and deliver breaking news to their readers.")],
      ['title'=>__("E-commerce Professionals"),'description'=>__("OpenAI's API can be used by e-commerce professionals to generate product descriptions and other marketing materials. These tools can help businesses speed up the content creation process, while ensuring that their product descriptions are accurate, compelling, and optimized for SEO.")],
      ['title'=>__("Educators"),'description'=>__("AI-based content generation tools can be used by educators to generate educational materials such as quizzes, flashcards, and study guides. These tools can help educators create personalized learning experiences for their students, making it easier for them to understand and retain the information.")],
      ['title'=>__("Social Media Managers"),'description'=>__("Social media managers can content generation tools to generate captions and social media posts that resonate with their audience. These tools can help managers save time and ensure that their social media channels are consistently active and engaging.")],
      ['title'=>__("Bloggers"),'description'=>__("Bloggers can use content generation tools to generate blog post ideas and outlines, as well as to help with actual writing. This can help entrepreneurs save time and resources while ensuring that their blog content is high-quality and engaging.")],
      ['title'=>__("SEO Experts"),'description'=>__("Can be used to generate optimized meta tags and descriptions, as well as to help with keyword research. This can help entrepreneurs improve their website's search engine ranking and drive more organic traffic to their site.")],
      ['title'=>__("Support Agents"),'description'=>__("Can be used to generate automated responses to frequently asked customer support questions. This can help entrepreneurs improve their customer service response times and ensure that their customers are satisfied with their experience.")],
    ];
  ?>
  <section id="about" class="pt-14 sm:pt-20 lg:pt-[130px]">
    <div class="px-4 xl:container-fluid">
      <!-- Section Title -->
      <div class="relative mx-auto mb-12 pt-6 text-center lg:mb-20 lg:pt-16" data-wow-delay=".2s">
        <span class="title"> {{__("Use Cases")}} </span>
        <h2 class="mx-auto mb-5 max-w-[570px] font-heading text-3xl font-semibold text-dark dark:text-white sm:text-4xl md:text-[50px] md:leading-[60px]"> {{__(":appname for Everyone",["appname"=>config('app.name')])}} </h2>
        <p class="mx-auto max-w-[570px] text-base text-dark-text"> {{__("Innovative use cases across industries and professions")}} </p>
      </div>

      <div class="relative z-10 overflow-hidden rounded px-8 pt-0 pb-8 md:px-[70px] md:pb-[70px] lg:px-[60px] lg:pb-[60px] xl:px-[70px] xl:pb-[70px]" data-wow-delay=".3s">
        <div class="absolute top-0 left-0 -z-10 h-full w-full bg-cover bg-center opacity-10 dark:opacity-40 bg-noise-pattern"></div>

        <div class="px-4 xl:container">
          <div class="-mx-4 flex flex-wrap items-center">
            @foreach($use_cases as $use_case)
            <div class="w-full px-3 lg:w-1/3">
              <div class="mx-auto mb-12 max-w-[530px] text-center lg:ml-1 lg:mb-0 lg:text-left">
                <h1 class="cs-use-case-heading mb-5 font-heading text-2xl font-semibold dark:text-white sm:text-4xl md:text-[500px] md:leading-[60px]" data-wow-delay=".3s"> {{$use_case['title']}}</h1>
                <p class="mb-12 text-base text-dark-text" data-wow-delay=".4s"> {{$use_case['description']}}</p>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ===== About Section End ===== -->

  <!-- ===== CTA Section Start ===== -->
  <section id="cta" class="pt-14 sm:pt-20 lg:pt-[130px]">
    <div class="px-4 xl:container">
      <div class="relative overflow-hidden bg-cover bg-center py-[60px] px-10 drop-shadow-light dark:drop-shadow-none sm:px-[70px]" data-wow-delay=".2s">
        <div class="absolute top-0 left-0 -z-10 h-full w-full bg-cover bg-center opacity-10 dark:opacity-40 bg-noise-pattern"></div>
        <div class="absolute bottom-0 left-1/2 -z-10 -translate-x-1/2">
          <svg width="1215" height="259" viewBox="0 0 1215 259" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.6" filter="url(#filter0_f_63_363)">
              <rect x="450" y="189" width="315" height="378" fill="url(#paint0_linear_63_363)" />
            </g>
            <defs>
              <filter id="filter0_f_63_363" x="0" y="-261" width="1215" height="1278" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                <feGaussianBlur stdDeviation="225" result="effect1_foregroundBlur_63_363" />
              </filter>
              <linearGradient id="paint0_linear_63_363" x1="420.718" y1="263.543" x2="585.338" y2="628.947" gradientUnits="userSpaceOnUse">
                <stop stop-color="#ABBCFF" />
                <stop offset="0.859375" stop-color="#4A6CF7" />
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div class="-mx-4 flex flex-wrap items-center">
          <div class="w-full px-4 lg:w-2/3">
            <div class="mx-auto mb-10 max-w-[550px] text-center lg:ml-0 lg:mb-0 lg:text-left">
              <h2 class="mb-4 font-heading text-xl font-semibold leading-tight text-dark dark:text-white sm:text-[38px]"> {{__("Now you know everything!")}} {{__("Ready to Join?")}} </h2>
              <p class="text-base text-dark-text"> {{__("Join to the future club of writing")}} </p>
            </div>
          </div>
          <div class="w-full px-4 lg:w-1/3">
            <div class="text-center lg:text-right">
              <a href="{{route("register")}}" class="inline-flex items-center rounded bg-primary py-[14px] px-8 font-heading text-base text-white hover:bg-opacity-90"> {{__("Get Started Now")}} </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ===== CTA Section End ===== -->

  <!-- ===== Testimonial Section Start ===== -->
  @include('landing.partials.testimonial')
  <!-- ===== Testimonial Section End ===== -->

@endsection

@push('script-footer')
  <script src="{{asset('/assets/landing/main.js')}}"></script>
@endpush
