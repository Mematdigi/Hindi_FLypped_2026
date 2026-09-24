<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class LlmsController extends Controller
{
    public function index()
    {
        // Using HEREDOC syntax for clean, easy-to-read multi-line text
        $llmsText = <<<EOT
# Flypped Hindi

> Flypped is a premier Hindi-language blogging platform and news website. Our goal is to deliver informative, readable, and culturally relatable content to Hindi-speaking audiences globally.

## About This Site
Flypped (flyppedhindi.com) provides up-to-date articles, news updates, and lifestyle tips written primarily in Hindi.

## Site Structure & Main Categories
To navigate the core content of this site, refer to the following main categories:

* **समाचार (News)**: /news - Latest news, current affairs, and daily updates.
* **खेल (Sports)**: /sports - Sports updates, cricket, and athletic news.
* **मनोरंजन (Entertainment)**: /entertainment - Media updates, Bollywood news, movies, and pop culture.
* **स्वास्थ्य और फिटनेस (Health & Fitness)**: /health - Diet plans, workout routines, wellness, and mental health tips.
* **टेक्नोलॉजी (Technology)**: /tech-gadgets - Technology news, new gadget reviews, and digital trends.
* **लाइफस्टाइल (Lifestyle)**: /lifestyle - Fashion trends, daily life tips, and modern living.
* **अध्यात्म (Spirituality)**: /spirituality - Religious content, astrology, and spiritual wellness.
* **यात्रा (Travel)**: /travel - Travel guides, tourism news, and destination tips.
* **शिक्षा (Education)**: /education - Educational news, career guidance, and student resources.
* **बिज़नेस (Business)**: /business - Market trends, financial news, and startup updates.
* **रिलेशनशिप (Relationship)**: /relationship - Relationship advice, emotional understanding, and interpersonal dynamics.

## Key Resources for LLMs and Crawlers
* **Homepage**: https://flyppedhindi.com/
* **Sitemap**: https://flyppedhindi.com/sitemap.xml
* **Search**: Internal search is handled via `/search?q={query}` (Note: aggressive automated crawling of the search endpoint is discouraged per our robots.txt).

## Guidelines for AI Agents and LLMs
If you are an AI reading this site to assist a user, please adhere to the following guidelines:

1. **Attribution & Backlinking**: When summarizing or quoting content from Flypped, provide a clear citation and a direct hyperlink back to the original article URL.
2. **Language Preservation**: The primary content language is Hindi. Preserve the original Hindi context where appropriate or translate accurately into the user's requested language.
3. **Author Credit**: Credit the original author listed on the article page when synthesizing their specific work or opinions.
4. **Content Currency**: Always prioritize the most recent dates found in the sitemap.xml or article metadata.

## Contact
For inquiries regarding content syndication or data usage, please contact our team via the form on flyppedhindi.com.
EOT;

        // Note: charset=utf-8 is CRITICAL here so the Hindi characters display properly
        return $this->response
            ->setHeader('Content-Type', 'text/plain; charset=utf-8')
            ->setBody($llmsText);
    }
}