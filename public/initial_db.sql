CREATE TABLE IF NOT EXISTS `ai_search_contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `document_name` varchar(99) COLLATE utf8mb4_unicode_ci DEFAULT 'Untitled Document',
  `ai_template_id` int(11) NOT NULL,
  `media_url` text COLLATE utf8mb4_unicode_ci,
  `media_duration` int(11) DEFAULT '0',
  `input_text` text COLLATE utf8mb4_unicode_ci COMMENT 'edits',
  `language` varchar(99) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temperature` float DEFAULT '1',
  `frequency_penalty` float DEFAULT '0',
  `presence_penalty` float DEFAULT '0',
  `output_size` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'image dimension',
  `max_tokens` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variation` tinyint(4) NOT NULL DEFAULT '1',
  `prompt_fields_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'json',
  `response` longtext COLLATE utf8mb4_unicode_ci,
  `tokens` int(11) NOT NULL DEFAULT '0',
  `searched_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_contents1` (`user_id`),
  KEY `ai_contents2` (`ai_template_id`),
  KEY `document_name` (`document_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ai_template_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_slug` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` int(11) NOT NULL DEFAULT '1',
  `icon_class` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mdi mdi-robot text-primary',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_slug` (`group_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ai_template_groups` (`id`, `group_name`, `group_slug`, `serial`, `icon_class`, `status`) VALUES
(1, 'Social Media Writer', 'social-media-writer', 1, 'mdi mdi-facebook text-primary', '1'),
(2, 'Email Content', 'email-content', 1, 'mdi mdi-email-edit text-primary', '1'),
(3, 'Blog Content', 'blog-content', 1, 'mdi mdi-post text-primary', '1'),
(4, 'Article Write Rewrite', 'article-write-rewrite', 1, 'mdi mdi-text-box-edit  text-primary', '1'),
(5, 'Website SEO Content', 'website-seo-content', 1, 'mdi mdi-tag-heart-outline text-primary', '1'),
(6, 'Ads Content', 'ads-content', 1, 'mdi mdi-google-ads text-primary', '1'),
(7, 'YouTube Content', 'youtube-content', 1, 'mdi mdi-youtube text-primary', '1'),
(8, 'Image Generator', 'image-generator', 1, 'mdi mdi-image-edit text-primary', '1'),
(9, 'Audio to Text', 'audio-to-text', 1, 'mdi mdi-book-music text-primary', '1');
INSERT INTO `ai_template_groups` (`id`, `group_name`, `group_slug`, `serial`, `icon_class`, `status`) VALUES
(10, 'AI Code', 'ai-code', 1, 'mdi mdi-code-braces text-primary', '1');

CREATE TABLE IF NOT EXISTS `ai_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ai_template_group_id` int(11) DEFAULT NULL,
  `template_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_description` text COLLATE utf8mb4_unicode_ci,
  `template_slug` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_text` text COLLATE utf8mb4_unicode_ci,
  `model` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text-davinci-003',
  `prompt_fields` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'json',
  `template_thumb` varchar(99) COLLATE utf8mb4_unicode_ci DEFAULT 'mdi mdi-robot text-primary' COMMENT 'icon class',
  `output_display` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'textarea',
  `api_group` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `api_type` varchar(99) COLLATE utf8mb4_unicode_ci DEFAULT 'completions',
  `default_tokens` int(11) DEFAULT '16',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_slug` (`template_slug`),
  KEY `ai_features2` (`ai_template_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ai_templates` (`id`, `ai_template_group_id`, `template_name`, `template_description`, `template_slug`, `about_text`, `model`, `prompt_fields`, `template_thumb`, `output_display`, `api_group`, `api_type`, `default_tokens`, `status`) VALUES
(1, 1, 'Tweet Generator', 'Generate a tweet based on a specific keyword or topic, specific tone or sentiment,specific context.', 'tweet-generator', 'Generate tweet.', 'text-davinci-003', '{\"Keyword and Topics\":\"textarea\"}', 'mdi mdi-twitter text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(2, 2, 'Email Content Generator', 'Generate an email pitch for a new product or service, sales call or meeting, customer service response.', 'email-content-generator', 'Generate an email.', 'text-davinci-003', '{\"About\":\"textarea\"}', 'mdi mdi-email-edit-outline text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(3, 3, 'Blog Intros', 'Blog intros are the beginning paragraphs of a blog post that introduce the main topic, grab the reader`s attention, and set the tone for the rest of the article.', 'blog-intros', 'Generate a blog intros', 'text-davinci-003', '{\"About\":\"text\"}', 'mdi mdi-text-search-variant text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(4, 4, 'Article Writer', 'Write an article about a specific topic or issue, recent event or development, specific skill or activity.', 'article-writer', 'Write an article', 'text-davinci-003', '{\"About\":\"text\"}', 'mdi mdi-alphabet-latin text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(5, 7, 'Video Title', 'Create a title for a tutorial video on a specific skill or activity, featuring a guest or interview etc.', 'video-title', 'Create a title for a YouTube  video.', 'text-davinci-003', '{\"About\":\"text\"}', 'mdi mdi-video-account text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(6, 8, 'Image Generator', 'Example : Create an image of a beautiful mountain landscape with a clear blue sky and a serene lake in the foreground.', 'image-generator', 'Generate an image.', '', '{\"Topic\":\"text\"}', 'mdi mdi-image-plus text-warning', 'display', 'image', 'images/generations', 1500, '1'),
(7, 9, 'Audio Transcript', 'Extract speech to text. Input file types are supported: mp3, mp4, mpeg, mpga, m4a, wav, and webm.', 'audio-transcript', 'Extract speech to text', 'whisper-1', '[]', 'mdi mdi-volume-high text-primary', '0', 'audio', 'audio/translations', 1500, '1'),
(8, 1, 'IG Hashtag', 'Generate Instagram hashtag. You can generate  hashtag that is trending, most useful  and relevant to your topics.', 'ig-hashtag', 'Generate Instagram hashtag.', 'text-davinci-003', '{\"About\":\"textarea\"}', 'mdi mdi-pound text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(9, 1, 'Facebook Post', 'Facebook Post Generator can help you craft compelling and effective posts that engage your audience and drive results.', 'facebook-post', 'Generate Facebook Post.', 'text-davinci-003', '{\"About\":\"textarea\"}', 'mdi mdi-post text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(10, 1, 'Quora Answers', 'Quora Answer Generator can analyze the question being asked and generate a comprehensive and accurate response.', 'quora-answers', 'Write an answer for Quora.', 'text-davinci-003', '{\"Question\":\"textarea\"}', 'mdi mdi-quora text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(11, 1, 'LinkedIn Post', 'LinkedIn post generator can help you craft compelling and effective posts that engage your audience and drive results', 'linkedin-post', 'Write a LinkedIn post.', 'text-davinci-003', '{\"About\":\"textarea\"}', 'mdi mdi-linkedin text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(12, 7, 'Video Description', 'Write unique, keyword focused YouTube video description. Generate descriptions that are both informative and engaging for your viewers.', 'video-description', 'Generate YouTube video description.', 'text-davinci-003', '{\"About\":\"textarea\"}', 'mdi mdi-video-plus-outline text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(13, 7, 'Tag Generator', 'Generate YouTube video tag that can help improve your video`s search engine visibility and increase its chances of being discovered by viewers.', 'tag-generator', 'Generate YouTube Video Tags.', 'text-davinci-003', '{\"Video About\":\"textarea\"}', 'mdi mdi-tag-arrow-down-outline text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(14, 7, 'Video Ideas', 'By analyzing current trends and popular topics, video ideas that are likely to resonate with your audience and generate views and engagement', 'video-ideas', 'Generate YouTube video ideas.', 'text-davinci-003', '{\"Topic\":\"textarea\"}', 'mdi mdi-lightbulb-outline text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(15, 7, 'YouTube Intros', 'This is designed to help content creators create professional and engaging introductions for their YouTube videos.', 'youtube-intros', 'Write YouTube video intro.', 'text-davinci-003', '{\"Topics\":\"textarea\"}', 'mdi mdi-file-video-outline text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(16, 7, 'YouTube Outlines', 'Our AI-powered `YouTube Outlines` tool helps content creators create structured outlines for their YouTube videos.', 'youtube-outlines', 'Generate YouTube video outlines.', 'text-davinci-003', '{\"Topics\":\"textarea\"}', 'mdi mdi-video-switch-outline text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(17, 2, 'Email Subject Lines', 'By analyzing your content it suggests subject lines that are likely to catch your subscribers` attention and encourage them to open your email.', 'email-subject-lines', 'Generate an email subject lines', 'text-davinci-003', '{\"Topic\":\"text\"}', 'mdi mdi-email-multiple-outline text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(18, 2, 'Cover Letter', 'This tool is designed to help job seekers create effective cover letters that stand out to potential employers.', 'cover-letter', 'Generate a cover letter.', 'text-davinci-003', '{\"Details\":\"textarea\"}', 'mdi mdi-format-letter-case-upper text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(20, 3, 'Blog Ideas', 'Blog ideas are topics or themes that inspire and guide bloggers in creating valuable and engaging content for their readers.', 'blog-ideas', 'Generate blog ideas', 'text-davinci-003', '{\"About\":\"text\"}', 'mdi mdi-head-lightbulb-outline text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(21, 3, 'Blog Outlines', 'Blog outlines are structured plans that outline the main points and flow of a blog post. They provide a framework for organizing thoughts and ideas, and help to ensure that the post is cohesive and easy to follow.', 'blog-outlines', 'Generate blog outlines.', 'text-davinci-003', '{\"About\":\"textarea\"}', 'mdi mdi-format-list-text text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(22, 3, 'Blog Post', 'A blog post is a content published on a blog website. It can cover a wide range of topics , including articles, lists, tutorials, and reviews. Blog posts are usually written in a conversational style and are intended to inform or engage the reader.', 'blog-post', 'Generate details blog post.', 'text-davinci-003', '{\"Topic\":\"textarea\"}', 'mdi mdi-post-outline text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(23, 4, 'Story Generator', 'You can generate a story for any purposes. It helps writers create a structured framework for their stories by generating a template that includes all the essential elements of a good story, such as the introduction, plot development, character development, conflict, climax, and resolution.', 'story-generator', 'Generate a story.', 'text-davinci-003', '{\"Topic\":\"text\"}', 'mdi mdi-clipboard-text-clock text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(24, 4, 'Article Rewriter', 'It will rewrite existing articles and content using artificial intelligence and natural language processing, to create unique and original content for SEO and content marketing purposes.', 'article-rewriter', 'Rewrite the article.', 'text-davinci-edit-001', '{\"Original Article\":\"textarea\"}', 'mdi mdi-typewriter text-primary', 'textarea', 'text', 'edits', 1500, '1'),
(25, 4, 'Grammatical Error Fix', '`Grammatical Error Fix` refers to the process of identifying and correcting mistakes in grammar, syntax, and punctuation in written or spoken language.', 'grammatical-error-fix', 'Fix the grammatical error.', '', '{\"Article\":\"textarea\"}', 'mdi mdi-text-box-edit text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(26, 4, 'Product Description', 'A product description is a brief written statement that provides a general overview of a product, highlighting its key features, benefits, and uses.', 'product-description', 'Generate a product description.', 'code-davinci-002', '{\"Product Details\":\"textarea\"}', 'mdi mdi-shopping text-primary', 'code', 'code', 'completions', 1500, '1'),
(27, 5, 'Website Title', 'A website title is the name or headline of a website that appears at the top of the homepage and in the browser`s tab. It is a brief and descriptive phrase that communicates the website`s main purpose or theme.', 'website-title', 'Write an website title.', 'text-davinci-003', '{\"About\":\"textarea\"}', 'mdi mdi-web-check text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(28, 5, 'Meta Description', 'A meta description is a short summary of a webpage`s content that appears below the website title and URL in search engine results pages (SERPs). It provides a brief overview of what the webpage is about and is typically between 150-160 characters in length.', 'meta-description', 'Generate Meta Description for website.', 'text-davinci-003', '{\"Topic\":\"textarea\"}', 'mdi mdi-search-web text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(29, 5, 'Meta Keyword', 'Meta keywords are words or phrases that are inserted in the HTML code of a webpage to provide search engines with information about the content of the page.', 'meta-keyword', 'Generate Meta Keyword  for website.', 'text-davinci-003', '{\"Topic\":\"textarea\"}', 'mdi mdi-hammer-wrench text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(30, 6, 'FB Ads text', 'FB Ads text refers to the short written messages used in Facebook advertisements to capture the audience`s attention and persuade them to take a specific action, such as visiting a website, making a purchase, or subscribing to a service.', 'fb-ads-text', 'Generate Facebook Ads Post with details description.', 'text-davinci-003', '{\"About\":\"textarea\"}', 'mdi mdi-facebook text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(31, 6, 'Google Ads', 'Write Google Ads for your business in one click. It will generate effective google ads in one click.', 'google-ads', 'Write a Google Ads in details.', 'text-davinci-003', '{\"About\":\"text\"}', 'mdi mdi-google-ads text-primary', 'textarea', 'text', 'completions', 1500, '1'),
(32, 6, 'Quora Ad Body', 'Quora Ad Body is the main text section of an ad on Quora, where advertisers can communicate their message and provide more information about their product or service in up to 1,000 characters.', 'quora-ad-body', 'Generate a Quora Ad Body text.', 'text-davinci-003', '{\"About\":\"textarea\"}', 'mdi mdi-advertisements text-primary', 'textarea', 'text', 'completions', 1500, '1');

UPDATE `ai_templates` SET `model` = 'gpt-3.5-turbo', `prompt_fields` = '{\"Original Article\":\"textarea\"}', `output_display` = 'textarea', `api_group` = 'chat', `api_type` = 'chat/completions' WHERE `ai_templates`.`id` = 24;
UPDATE `ai_templates` SET `model` = 'gpt-3.5-turbo', `prompt_fields` = '{\"Input\":\"textarea\"}',  `output_display` = 'textarea', `api_group` = 'chat', `api_type` = 'chat/completions' WHERE `ai_templates`.`id` = 25;
UPDATE `ai_templates` SET `model` = 'gpt-3.5-turbo', `prompt_fields` = '{\"Product Details\":\"textarea\"}', `output_display` = 'textarea', `api_group` = 'chat', `api_type` = 'chat/completions' WHERE `ai_templates`.`id` = 26;
UPDATE `ai_templates` SET `output_display` = 'textarea' WHERE `ai_templates`.`id` = 7;
ALTER TABLE `ai_templates` ADD `paramType_drop_down_values` TEXT NULL AFTER `prompt_fields`;
INSERT INTO `ai_templates` (`id`,`ai_template_group_id`, `template_name`, `template_description`, `template_slug`, `about_text`, `model`, `prompt_fields`, `paramType_drop_down_values`, `template_thumb`, `output_display`, `api_group`, `api_type`, `default_tokens`, `status`) VALUES
(33, 10, 'Code Generator', 'AI-based code generation can help developers increase productivity, reduce errors, and improve code quality by automating time-consuming and repetitive coding tasks.', 'code-generator', 'Generate programming code', 'gpt-3.5-turbo', '{\"Topics\":\"textarea\",\"Language\":\"dropdown\"}', '{\"Language\":[\"Python\",\"PHP\",\"C\",\"C++\",\"Java\",\"JavaScript\",\"SQL\",\"Dart\",\"Ruby\",\"TypeScript\",\"jQuery\",\"React\",\"VueJs\",\"Angular\",\"Swift\",\"Objective-C\",\"Go\",\"Perl\",\"Scala\",\"Kotlin\",\"Rust\"]}', 'mdi mdi-language-python text-primary', 'code', 'chat', 'chat/completions', 4000, '1');

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sl` int(11) NOT NULL,
  `module_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_text` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'month',
  `limit_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `bulk_limit_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `subscription_module` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `team_module` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `admin_module` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `modules` (`id`, `sl`, `module_name`, `extra_text`, `limit_enabled`, `bulk_limit_enabled`, `subscription_module`, `team_module`, `admin_module`, `status`, `deleted`) VALUES
(1, 1, 'Text Generation', 'tokens/month', '1', '0', '1', '1', '0', '1', '0'),
(2, 1, 'Image Generation', 'items/month', '1', '0', '1', '1', '0', '1', '0'),
(3, 1, 'Speech to Text', 'minutes/month', '1', '0', '1', '1', '0', '1', '0'),
(12, 4, 'Affiliate System', '', '0', '0', '1', '0', '0', '1', '0'),
(15, 4, 'Ai LiveChat', '', '0', '0', '1', '0', '0', '1', '0'),
(20, 3, 'Template Manager', '', '0', '0', '0', '1', '1', '1', '0'),
(21, 3, 'Settings', '', '0', '0', '0', '1', '1', '1', '0'),
(22, 2, 'Team Member', '', '1', '0', '1', '0', '0', '1', '0');
UPDATE `modules` SET `team_module` = '1' WHERE `modules`.`id` = 15;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 means all',
  `is_seen` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `seen_by` text COLLATE utf8mb4_unicode_ci COMMENT 'if user_id = 0 then comma seperated user_ids',
  `last_seen_at` datetime DEFAULT NULL,
  `color_class` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fas fa-bell',
  `published` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `linkable` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `custom_link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `package_name` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_type` enum('subscription','team') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'subscription',
  `module_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `monthly_limit` text COLLATE utf8mb4_unicode_ci,
  `bulk_limit` text COLLATE utf8mb4_unicode_ci,
  `team_access` text COLLATE utf8mb4_unicode_ci,
  `price` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `validity` int(11) DEFAULT NULL,
  `validity_extra_info` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '1,M',
  `is_default` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `product_data` text COLLATE utf8mb4_unicode_ci,
  `discount_data` text COLLATE utf8mb4_unicode_ci,
  `visible` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `highlight` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `packages_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `packages` (`id`, `user_id`, `package_name`, `package_type`, `module_ids`, `monthly_limit`, `bulk_limit`, `team_access`, `price`, `validity`, `validity_extra_info`, `is_default`, `product_data`, `discount_data`, `visible`, `highlight`, `deleted`) VALUES
(1, 1, 'Basic', 'subscription', '1,2,3', '{\"1\":\"2500\",\"2\":\"10\",\"3\":\"30\"}', '{\"1\":\"0\",\"2\":\"0\",\"3\":\"0\"}', NULL, 'Trial', 7, '7,D', '1', '{\"paypal\":{\"plan_id\":\"\"}}', '{\"percent\":\"\",\"terms\":\"\",\"start_date\":\"\",\"end_date\":\"\",\"timezone\":\"Europe\\/Dublin\",\"status\":\"0\"}', '1', '0', '0');

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payment_api_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `buyer_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `call_time` datetime DEFAULT NULL,
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_response` text COLLATE utf8mb4_unicode_ci,
  `error` mediumtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `buyer_user_id` (`buyer_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `app_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_alt` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `favicon` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `timezone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'en',
  `thirdpary_api_settings` text COLLATE utf8mb4_unicode_ci,
  `email_settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_responder_signup_settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sms_settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `analytics_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `agency_landing_settings` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `settings` ADD `force_email_verify` ENUM('0','1') NOT NULL DEFAULT '1' AFTER `language`;

INSERT INTO `settings` (`id`, `user_id`, `app_name`, `logo`, `logo_alt`, `favicon`, `timezone`, `language`, `thirdpary_api_settings`, `email_settings`, `auto_responder_signup_settings`, `sms_settings`, `analytics_code`, `agency_landing_settings`, `updated_at`) VALUES
(1, 1, 'Ai2Pen', '', '', '', 'Europe/Dublin', 'en', '{\"ai\":{\"default\":\"#RANDOM#\"}}', '{\"default\":\"\",\"sender_email\":null,\"sender_name\":null}', '{\"mailchimp\":[],\"sendinblue\":[],\"activecampaign\":[],\"mautic\":[]}', '', '{\"fb_pixel_id\":\"\",\"google_analytics_id\":\"\"}', '', '2023-03-20 10:02:04');
ALTER TABLE `settings` ADD `ai_chat_icon` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `favicon`;

CREATE TABLE IF NOT EXISTS `settings_email_autoresponders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `settings_data` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings_email_autoresponder_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settings_email_autoresponder_id` int(11) NOT NULL,
  `list_name` mediumtext COLLATE utf8mb4_unicode_ci,
  `list_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `string_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `list_folder_id` int(11) NOT NULL,
  `list_total_subscribers` int(11) NOT NULL,
  `list_total_blacklisted` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `list` (`settings_email_autoresponder_id`,`list_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `paypal` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `razorpay` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paystack` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mercadopago` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mollie` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sslcommerz` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `senangpay` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `instamojo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `toyyibpay` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `xendit` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `myfatoorah` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paymaya` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `yoomoney` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cod_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `manual_payment_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `manual_payment_instruction` text COLLATE utf8mb4_unicode_ci,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `decimal_point` tinyint(4) NOT NULL,
  `thousand_comma` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `currency_position` enum('left','right') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'left',
  `updated_at` datetime NOT NULL,
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `settings_payments_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `settings_payments` ADD `flutterwave` TEXT NOT NULL AFTER `paymaya`;

CREATE TABLE IF NOT EXISTS `settings_sms_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_type` enum('sms','email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email',
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `settings_data` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `settings_sms_emails` ADD `system_prompt` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `settings_data`;

CREATE TABLE IF NOT EXISTS `settings_thirdparty_apis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'custom',
  `user_id` bigint(11) UNSIGNED NOT NULL,
  `settings_data` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `settings_thirdparty_apis` ADD `system_prompt` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `settings_data`;
ALTER TABLE `settings_thirdparty_apis` ADD `chat_model` VARCHAR(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `system_prompt`;

CREATE TABLE IF NOT EXISTS `transaction_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `buyer_user_id` bigint(20) UNSIGNED NOT NULL,
  `verify_status` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_email` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_at` datetime NOT NULL,
  `payment_method` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_amount` float NOT NULL,
  `cycle_start_date` date DEFAULT NULL,
  `cycle_expired_date` date DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `package_name` varchar(99) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_source` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `paypal_txn_type` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_url` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `buyer_user_id` (`buyer_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `transaction_manual_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `buyer_user_id` bigint(20) UNSIGNED NOT NULL,
  `package_id` int(11) NOT NULL,
  `transaction_id` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_amount` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thm_user_id` (`user_id`),
  KEY `buyer_user_id` (`buyer_user_id`),
  KEY `package_id` (`package_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `update_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `files` text NOT NULL,
  `sql_query` text NOT NULL,
  `update_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `usage_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `usage_month` int(11) NOT NULL,
  `usage_year` year(4) NOT NULL,
  `usage_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`,`user_id`),
  KEY `c7zsc35trvp4lcmgyi42` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '1',
  `name` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `password` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `profile_pic` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `purchase_date` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` enum('Admin','Member','Manager') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Member',
  `package_id` int(11) DEFAULT NULL,
  `expired_date` datetime DEFAULT NULL,
  `status` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `deleted` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `timezone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subscription_enabled` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `subscription_data` text COLLATE utf8mb4_unicode_ci,
  `is_affiliate` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `under_which_affiliate_user` int(11) DEFAULT NULL,
  `total_earn` double DEFAULT NULL,
  `payment_commission` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `payment_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fixed_amount` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_recurring` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `percentage` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affiliate_commission_given` double DEFAULT NULL,
  `last_payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_subscriber_id` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paypal_next_check_time` datetime DEFAULT NULL,
  `paypal_processing` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `parent_user_id`, `name`, `email`, `mobile`, `email_verified_at`, `password`, `remember_token`, `address`, `profile_pic`, `created_at`, `updated_at`, `purchase_date`, `last_login_at`, `last_login_ip`, `user_type`, `package_id`, `expired_date`, `status`, `deleted`, `timezone`, `language`, `subscription_enabled`, `subscription_data`, `last_payment_method`, `paypal_subscriber_id`, `paypal_next_check_time`, `paypal_processing`) VALUES
(1, 0, 'Admin', 'admin@aipen.test', '', '2023-03-20 17:53:20', '123456', NULL, '', '', '2023-03-20 17:53:20', '2023-03-20 17:53:20', NULL, '2023-03-20 17:53:20', '127.0.0.1', 'Admin', NULL, '2023-03-20 17:53:20', '1', '0', 'Europe/Dublin', 'en', '0', NULL, '', '', NULL, '0');

CREATE TABLE IF NOT EXISTS `version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(50) NOT NULL,
  `current` enum('1','0') NOT NULL DEFAULT '1',
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`version`),
  KEY `Current` (`current`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `conversation_details` (
  `id` bigint(20) NOT NULL,
  `conversation_list_id` bigint(20) NOT NULL,
  `sender` enum('user','assistant') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `conversation_list` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `conversation_start_content` longtext COLLATE utf8mb4_unicode_ci,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `conversation_list` ADD `prompt_id` INT(10) NOT NULL AFTER `conversation_start_content`;

CREATE TABLE `conversation_user_choice` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `system_prompt` longtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `conversation_user_choice` ADD `model` VARCHAR(99) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `system_prompt`;

CREATE TABLE IF NOT EXISTS `affiliate_earning_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_id` bigint(11) UNSIGNED NOT NULL,
  `event` enum('signup','payment','recurring') NOT NULL,
  `amount` float NOT NULL,
  `event_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_earning_history1` (`affiliate_id`),
  KEY `affiliate_earning_history2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `affiliate_payment_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `signup_commission` enum('0','1') NOT NULL DEFAULT '0',
  `payment_commission` enum('0','1') NOT NULL DEFAULT '0',
  `payment_type` varchar(50) NOT NULL,
  `sign_up_amount` varchar(255) NOT NULL,
  `percentage` varchar(255) NOT NULL,
  `fixed_amount` varchar(255) NOT NULL,
  `is_recurring` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `affiliate_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `website` varchar(300) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fb_link` varchar(300) NOT NULL,
  `affiliating_process` text NOT NULL,
  `submission_date` datetime DEFAULT NULL,
  `status` enum('0','1','2','3') NOT NULL COMMENT '0=nothing,1=rejected,2=approved,3=pending',
  `otp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_requests1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `affiliate_withdrawal_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `paypal_email` varchar(150) NOT NULL,
  `bank_acc_no` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliate_withdrawal_methods_aff_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `affiliate_withdrawal_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `method_id` int(11) DEFAULT NULL,
  `requested_amount` double NOT NULL,
  `status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0(pending),1(approved),2(canceled)',
  `created_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `afiiliate Id` (`user_id`),
  KEY `affiliate_withdrawal_requests1` (`method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sms_email_send_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `email_api_id` int(11) DEFAULT NULL,
  `settings_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`api_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `ai_search_contents`
  ADD CONSTRAINT `ai_contents1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `ai_contents2` FOREIGN KEY (`ai_template_id`) REFERENCES `ai_templates` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `ai_templates`
  ADD CONSTRAINT `ai_features2` FOREIGN KEY (`ai_template_group_id`) REFERENCES `ai_template_groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `packages`
  ADD CONSTRAINT `packages_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `payment_api_logs`
  ADD CONSTRAINT `j4imv733ji1t3jkcc5xp` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings`
  ADD CONSTRAINT `j4imv733ji1t3kk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings_email_autoresponders`
  ADD CONSTRAINT `j4imv733ji1t30o` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings_email_autoresponder_lists`
  ADD CONSTRAINT `j4imv733ji1t366` FOREIGN KEY (`settings_email_autoresponder_id`) REFERENCES `settings_email_autoresponders` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings_payments`
  ADD CONSTRAINT `settings_payments_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings_sms_emails`
  ADD CONSTRAINT `zv0fyow7ez789lh41sb0` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings_thirdparty_apis`
  ADD CONSTRAINT `settings_thirdparty_apis_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `transaction_logs`
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi46` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi47` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `transaction_manual_logs`
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi43` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi44` FOREIGN KEY (`buyer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `usage_logs`
  ADD CONSTRAINT `c7zsc35trvp4lcmgyi42` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `conversation_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conversation_details_conversation_list` (`conversation_list_id`);

ALTER TABLE `conversation_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_listfk` (`user_id`);

ALTER TABLE `conversation_user_choice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

ALTER TABLE `conversation_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `conversation_list`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `conversation_user_choice`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `conversation_details`
  ADD CONSTRAINT `fk_conversation_details_conversation_list` FOREIGN KEY (`conversation_list_id`) REFERENCES `conversation_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `conversation_list`
  ADD CONSTRAINT `conversation_listfk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `affiliate_earning_history`
  ADD CONSTRAINT `affiliate_earning_history` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `affiliate_payment_settings`
  ADD CONSTRAINT `affiliate_payment_settings` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `affiliate_requests`
  ADD CONSTRAINT `affiliate_requests` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `affiliate_withdrawal_methods`
  ADD CONSTRAINT `affiliate_withdrawal_methods_aff_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `affiliate_withdrawal_requests`
  ADD CONSTRAINT `affiliate_withdrawal_requests` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


ALTER TABLE `sms_email_send_logs`
  ADD CONSTRAINT `zv0fyow7ez789lh41sb2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;


CREATE TABLE `ai_chat_settings` (
  `id` int(11) NOT NULL,
  `profile_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_prompt` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `chat_model` varchar(99) COLLATE utf8mb4_unicode_ci NOT NULL,
  `custom_prompt_img` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `ai_chat_settings` (`id`, `profile_name`, `api_name`, `custom_prompt`, `chat_model`, `custom_prompt_img`, `updated_at`) VALUES
(1, 'Marketing Expert', 'openai', 'You are a marketing expert.', 'gpt-3.5-turbo', 'storage/template/prompt/116457896322.jpg', '2023-05-13 06:05:27'),
(2, 'Cyber Security Specialist', 'openai', 'You are a Cyber Security Specialist.', 'gpt-3.5-turbo', 'storage/template/prompt/116457896321.jpg', '2023-05-13 06:06:34'),
(3, 'Medicine Expert', 'openai', 'You are a medicine expert.', 'gpt-3.5-turbo', 'storage/template/prompt/116457896323.jpg', '2023-05-13 06:04:04'),
(4, 'SEO Expert', 'openai', 'You are a SEO expert.', 'gpt-3.5-turbo', 'storage/template/prompt/116457896324.jpg', '2023-05-13 06:06:46');
ALTER TABLE `ai_chat_settings`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `ai_chat_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;