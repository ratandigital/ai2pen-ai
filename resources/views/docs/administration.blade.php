@extends('layouts.docs')
@section('title',__('Administration'))
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <ul id="submenu">
        <li><a href="#template-manager">{{ __("Template Manager") }}</a></li>
        <li><a href="#user-manager">{{ __("User & Team Manager") }}</a></li>
        <li><a href="#package-manager">{{ __("Package & Role Manager") }}</a></li>
        <li><a href="#general-settings">{{ __("General Settings") }}</a></li>
        <li><a href="#api-settings">{{ __("AI API Integration") }}</a></li>
        <li><a href="#email-settings">{{ __("Email Integration") }}</a></li>
        <li><a href="#auto-responder-settings">{{ __("Responder Integration") }}</a></li>
        <li><a href="#analytics-settings">{{ __("Analytics Integration") }}</a></li>
        <li><a href="#cron-settings">{{ __("Cron Job") }}</a></li>
        <li><a href="#payment-settings">{{ __("Payment Integration") }}</a></li>
        <li><a href="#landing-settings">{{ __("Landing Page Editor") }}</a></li>
        <li><a href="#language-settings">{{ __("Language Editor") }}</a></li>
      </ul>
      <div class="section-header text-center">
        <h1 class="main-header">{{ __("Administration") }} ({{ __("Admin User") }})</h1>
      </div>
      <hr  class='main-hr'/>
      <p> {!! __("To make the system functional, you must integrate the OpenAI API. Additionally, you need to incorporate an email provider for sending system emails such as sign up, email verification, etc. Moreover, you must add an email auto-responder profile to synchronize user emails. Finally, to receive payments, you need to integrate payment method APIs.") !!}
      </p>
      <p> {!! __("Here we will show you how to integrate third-party APIS with :appname.",['appname'=>config('app.name')]) !!}
        <p>{{__("We will also demonstrate other administrative tools like template management, user/role management, general settings, cron settings, landing page settings and multi-lingual editor.")}}</p>
      </p>
      <div class="alert alert-warning my-3"> {!! __("Regular license users will not have access to all the administrative tools we are going to discuss. Some of these tools are only accessible to extended license holders.") !!}
      </div>

      

      <div class="section-header">
        <h1 id="template-manager">{{ __("Template Manager") }}</h1>
      </div>

      <iframe width="100%" height="550px" src="https://www.youtube.com/embed/mw0LDpB3ZtI" title="How to Create Custom Template for AI2PEN" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
      
      <hr />
      <p> {{ __("As you may be aware, these tools are based on OpenAI's content generation technology. This powerful AI platform can generate many types of content. We have included the most popular, useful, and important content templates for your convenience. However, the potential of OpenAI is limitless. You can add your own templates and use them to generate custom content.") }}
      </p>
      <div class="row">
        <div class="col-12 col-lg-6"><img src="{{asset('assets/docs/images/settings-template1.png')}}" class="img-fluid"/></div>
        <div class="col-12 col-lg-6"><img src="{{asset('assets/docs/images/settings-template.png')}}" class="img-fluid"/></div>
      </div>
      <p>{{__("To add a template group, you only need to provide a group name. You can also add an icon class for the group using FontAwesome or MaterialIcon. The groups will be listed in order of priority based on their serial number, with lower numbers having higher priority.")}}</p>
      <p>{{__("First, you need to choose the API type from Text, Code, Image, and Speech-to-Text.")}}</p>
      <p>{{__("The Text API type is useful for generating textual content such as blogs, articles, titles, headers, etc. While the Text API can generate code, we use a special model specifically optimized for code writing, as recommended by OpenAI.")}}</p>
      <p>{{__("For your convenience, we have grouped all the AI models provided by OpenAI and linked them with API types. To use these models, all you have to do is select an API type and an AI model from the list.")}}</p>
      <p>{{__("The prompt intro text is a crucial field and is required for training OpenAI's models. The AI model will read this text to gain an understanding of your desired content. It serves as the foundation for the model's training data and is essential for generating accurate and relevant content.")}}</p>
      <p>{{__("You can also include custom parameters to help the AI model better understand your query. When a user utilizes this template, they can fill in the parameters with their respective values. This can help to provide more specific and accurate results tailored to the user's needs.")}}</p>
      <div class="row">
        <div class="col-12 col-lg-4"><img src="{{asset('assets/docs/images/settings-template2.png')}}" class="img-fluid img-thumbnail"/></div>
        <div class="col-12 col-lg-8"><img src="{{asset('assets/docs/images/settings-template3.png')}}" class="img-fluid img-thumbnail"/></div>
      </div>


      <div class="section-header">
        <h1 id="user-manager">{{ __("User & Team Manager") }} </h1>
      </div>
      <hr />
      <p>{{__("As previously mentioned, the extended license of this app includes a multi-user platform, unlocking a range of features relevant to SaaS businesses. One of these features is the User Manager, which allows users to sign up directly or for the admin to manage users from the panel. Team Users are not separate users, but rather an additional instance of an admin login that can perform specific tasks on behalf of the admin. In contrast, the SaaS version allows the admin to have team members and end-users to have team users as well.")}}</p>
      <p>{{__("Managing users and teams involves enabling or disabling logins, as well as editing and deleting users or teams. The process for adding users or teams is quite similar, with the form requiring a name, email, and package/role. However, for users, an additional value called an expiry date is necessary, which specifies the date after which they can no longer use the feature. Teams, on the other hand, do not have an expiry date.")}}</p>
      <img src="{{asset('assets/docs/images/settings-user.png')}}" class="col-12 col-lg-8 img-fluid img-thumbnail"/>

      <div class="section-header">
        <h1 id="package-manager">{{ __("Package & Role Manager") }} </h1>
      </div>
      <hr />
      <p>{{__("The pricing table displays the package or subscription plan which users can purchase and use the app for a specified duration. On the other hand, the role is the access level for team users.")}}</p>
      <p>{{__("Managing packages and roles involves editing and deleting tasks. Adding packages or roles involves a similar process, where the access level needs to be specified in the form. However, for subscription packages, additional information such as package price and validity period is required. You can also specify discount for a subscription package.")}}</p>
      <p class="fw-bold text-primary">{{__("The text generation module of the package refers to the number of tokens that a user can utilize for text generation within a specified monthly period. You can think of tokens as pieces of words used for natural language processing. For English text, 1 token is approximately 4 characters or 0.75 words. As a point of reference, the collected works of Shakespeare are about 900,000 words or 1.2M tokens.")}}</p>
      <p>{{__("The image generation module is quantified by the number of images/month that can be generated, while the speech-to-text module is measured in terms of minutes/month.")}}</p>
      <img src="{{asset('assets/docs/images/settings-package.png')}}" class="col-12 col-lg-10 img-fluid img-thumbnail"/>




      <div class="section-header">
        <h1 id="settings">{{ __("Settings") }}</h1>
      </div>
      <hr />
      <p> {{ __("On this page, you have the option to configure all the settings required for the application to function properly.") }}
      </p>
      <img src="{{asset('assets/docs/images/settings.png')}}" class="img-fluid"/>

      <div class="section-header text-center mt-5">
        <h2 class="main-header mb-0 pt-5" id="general-settings">{{ __("General Settings") }}</h2>
      </div>
      <p class="mt-0">{{__("Set your brand name,logo, favicon and preference (locale,timezone)")}}</p>
      <p>{{__("The application will display your brand and operate according to the locale and timezone that you specify. Additionally, the app offers RTL language support based on the locale you select, with RTL mode activating automatically. As for example, if you choose `Arabic`, the app will turn on RTL mode.")}}</p>

      <div class="section-header text-center mt-5">
        <h2 class="main-header mb-0 pt-5" id="api-settings">{{ __("AI API Integration") }}</h2>
      </div>
      <p class="mt-0">{!!__("As you know, this app is powered by OpenAI and needs OpenAI key to run. Go to :openai and click `Create new secret key`. If you haven`t already done so, you will need to sign up or sign in to proceed. This OpenAI API key will be used to power the system.",["openai"=>"<a href='https://platform.openai.com/account/api-keys' target='_BLANK'>https://platform.openai.com/account/api-keys</a>"])!!}</p>
      <p>{{ __("To add this API key to the application, click on the New button. You have the option to include multiple API keys and specify which one you wish to utilize. Additionally, you may opt to randomly use any of the added API keys for each use. Do not forget to `Save Changes`") }}</p>
      <img src="{{asset('assets/docs/images/settings-api2.png')}}" class="col-12 col-lg-8 img-fluid img-thumbnail mb-4"/>
      <img src="{{asset('assets/docs/images/settings-api.png')}}" class="img-fluid"/>

      <div class="section-header text-center mt-5">
        <h2 class="main-header mb-0 pt-5" id="email-settings">{{ __("Email Integration") }}</h2>
      </div>
      <p class="mt-0">{{__("Email settings will be used for several system emails like sign up, email verification, password reset etc.")}}</p>
      <p>{{__("By default, the application will utilize the PHP mail function for sending emails until you provide your own email profile. If you do not intend to add a custom profile, please ensure that your PHP mail is functioning correctly. You have the option to include multiple email profiles and designate a default profile, informing the app which profile you prefer to use.")}}</p>

      <p> {{ __("We support various email providers including SMTP,  Mailgun, Postmark, SES and Mandril. To add a email profile to the application, click on the New button. Do not forget to `Save Changes`.") }}
      </p>
      <img src="{{asset('assets/docs/images/settings-email.png')}}" class="img-fluid"/>

      <div class="section-header text-center mt-5">
        <h2 class="main-header mb-0 pt-5" id="auto-responder-settings">{{ __("Auto-responder Integration") }}</h2>
      </div>
      <p class="mt-0">{{__("An autoresponder API, such as Mailchimp,Sendinblue, ActiveCampaign, Mautic are powerful tool for businesses to automate their email marketing campaigns. With this technology, you can set up targeted email sequences, create personalized content, and deliver messages to your audience based on their behavior and interests. The API also provides analytics and reporting features, allowing you to measure the success of your campaigns and make data-driven decisions for future marketing efforts. Overall, an autoresponder API can save time, increase engagement, and ultimately drive revenue for your business.")}}</p>
      <p>{{__("On this page, you have the ability to configure your autoresponder profile to automatically add any new user who signs up and verifies their email address to your email list. This enables you to seamlessly integrate your email marketing efforts with your user acquisition process, ensuring that your subscribers receive timely and relevant content from your brand.")}}</p>
      <p>{{__("To add your autoresponder profile, simply click on the New button, and your autoresponder list will be automatically synced. From there, you can specify which list you wish to use and then save your changes.")}}</p>
      <img src="{{asset('assets/docs/images/settings-responder.png')}}" class="img-fluid"/>

      <div class="section-header text-center mt-5">
        <h2 class="main-header mb-0 pt-5" id="analytics-settings">{{ __("Analytics Scripts Integration") }}</h2>
      </div>
      <p class="mt-0">{{__("The Facebook Pixel is a code snippet that is added to your website, which enables you to track conversions, optimize ads, and build targeted audiences for your Facebook advertising campaigns. The Pixel collects data on user behavior, such as page views, purchases, and sign-ups, and then uses this information to improve the performance of your Facebook ads.")}}</p>
      <p class="">{{__("Similarly, Google Analytics code is a tracking code that is added to your website to provide insights into user behavior and website performance. This code allows you to monitor website traffic, measure user engagement, and identify areas for improvement in your website design and content. With Google Analytics, you can gain valuable insights into your audience and optimize your website for maximum engagement and conversion.")}}</p>
      <p>{{__("Overall, both Facebook Pixel and Google Analytics code are powerful tools that can help businesses make data-driven decisions to improve their online presence and drive revenue.")}}</p>
      <p>{{__("To begin tracking your website visitors with Facebook Pixel or Google Analytics, simply add the code (not the full JS scipt only ID) provided by the respective platform. Once the code is added, the analytics script will be automatically loaded on your app landing page, allowing you to start gathering valuable data on your website traffic and user behavior. With this information, you can make informed decisions about your marketing strategy and optimize your website to improve engagement and drive conversions.")}}</p>
      <img src="{{asset('assets/docs/images/settings-analytics.png')}}" class="col-12 col-lg-6 img-fluid"/>

      <div class="section-header text-center mt-5">
        <h2 class="main-header mb-0 pt-5" id="cron-settings">{{ __("Cron Job Commands") }}</h2>
      </div>
      <p class="mt-0">{{__("A cron job, also known as a cron schedule or cron task, is a command or script that is scheduled to run automatically at specified intervals on a Unix-based operating system. Cron jobs are commonly used for automating repetitive tasks such as data backups, database maintenance, and file system cleanup. The intervals for running these tasks can be set in minutes, hours, days, weeks, or months, allowing for precise control over when they occur. Cron jobs are executed by the Cron daemon, a background service that constantly monitors the system clock and checks for scheduled tasks to run. When a scheduled task is detected, the Cron daemon executes the associated command or script.")}}</p>
      <p class="">{{__("This system needs cron job to manage PayPal subscription api calls (extended license only) and to free-up storaged junk data on server periodically.")}}</p>
      <p><a target="_BLANK" href="https://blog.cpanel.com/how-to-configure-a-cron-job/#:~:text=Configuring%20Cron%20Jobs%20in%20cPanel,Week%20or%20Once%20Per%20Month.">{{__("This is how you can setup cron job in cpanel")}}</a></p>

      <div class="section-header text-center mt-5">
        <h2 class="main-header mb-0 pt-5" id="payment-settings">{{ __("Payment Settings") }} ({{__("Limited access for Regular License")}})</h2>
      </div>
      <p class="mt-0">{{__("You can leverage the power of a multi-user SaaS platform to sell this app as a service to end-users. SaaS allows you the ability to offer the app as a subscription-based service, enabling you to generate revenue from multiple users.")}}</p>
      <p class="">{{__("To facilitate this process, we have integrated several world-leading payment methods that can be used to receive subscriptions from end-users. This ensures that you have access to a wide range of payment options that are trusted and secure, providing a seamless experience for your customers. Overall, the extended license provides a comprehensive solution for businesses looking to monetize their app and leverage the benefits of a multi-user SaaS platform")}}</p>
      <p class="">{{__("We curretly support different payment methods including PayPal, Stripe, YooMoney, RazorPay, PayStack, Mollie, ToyyibPay, PayMaya, InstaMojo, Xendit, MyFatoorah etc. Add the payment method you want to use and setup the currency and save.")}}</p>
      <div class="alert alert-warning">
        <h5 class="">{{__("The regular license does not grant access to the payment APIs, limiting it to only manual payment options.")}}</h5>
      </div>
      <img src="{{asset('assets/docs/images/settings-payment.png')}}" class="img-fluid"/>


      <div class="section-header text-center mt-5">
        <h2 class="main-header mb-0 pt-5" id="landing-settings">{{ __("Landing Page Settings") }}</h2>
      </div>
      <p class="mt-0">{{__("As a SaaS system, it's essential to have a professional and engaging landing page to showcase your app to the world. But don't worry, we've got you covered! Our team has designed a beautiful, responsive, SEO friendly , and resourceful landing page that is fully customizable to fit your unique branding and messaging needs.In addition, our landing page is also supported by a dark mode feature, which provides a sleek and modern look that will appeal to users who prefer a darker color scheme. Overall, our landing page is the perfect tool to help you promote your SaaS system and drive user engagement and adoption.")}}</p>
      <p>{{__("This page allows you to customize your company information and optimize your SEO meta data. You can also replace existing media on your landing page and display customer reviews to increase engagement. Use can use `Language Editor` to change landing page content which will be discussed later in this article.")}}</p>
      <p>{{__("You have the option to turn off the review section and enable dark mode on your landing page. Disabling the landing page can be beneficial if you want to use a custom-built landing page. If you prefer to use a custom-built landing page, you can install this app on a sub-domain and install the custom landing page on the main domain. Then, in the settings, you can disable the loading of the landing page on this app.")}}</p>
      <img src="{{asset('assets/docs/images/settings-landing.png')}}" class="img-fluid"/>

      <div class="section-header text-center mt-5">
        <h2 class="main-header mb-0 pt-5" id="language-settings">{{ __("Language Editor") }}</h2>
      </div>
      <p class="mt-0">{{__("By default, the app runs in English, but you can change the language from the general settings. If you need to add a language or edit existing content, you can use these tools to manage the language and content. These tools are very user-friendly and do not require detailed instructions to use.")}}</p>
      <ul class="list-group ms-5">
        <li class="">{{__("Click on the Add button to add language. Select a language you want to add and click on the Save button.")}}</li>
        <li class="">{{__("You can delete a language by clicking on the Trash icon.")}}</li>
        <li class="">{{__("Click on the eye icon to provide/edit translation.")}}</li>
        <li class="">{{__("After providing translated text you must click on the `Apply Changes` button.")}}</li>
        <li class="">{{__("To download the translated text as json format, click on the Download button.")}}</li>
      </ul>
      <p></p>     

      <img src="{{asset('assets/docs/images/language/1.png')}}" class="img-fluid mb-3" />
      <img src="{{asset('assets/docs/images/language/2.png')}}" class="img-fluid mb-3" />
      <img src="{{asset('assets/docs/images/language/3.png')}}" class="img-fluid mb-3" />
      <img src="{{asset('assets/docs/images/language/4.png')}}" class="img-fluid mb-3" />


    </p>
  </section>
</div>
@endsection
