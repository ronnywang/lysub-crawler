<?php

$file_get_contents = function($u) {
    error_log("fetch {$u}");
    return file_get_contents($u);
};
$start_point = 'https://www.ly.gov.tw/Pages/List.aspx?nodeid=166';
$doc = new DOMDocument;
@$doc->loadHTML($file_get_contents($start_point));
foreach ($doc->getElementsByTagName('ul') as $ul_dom) {
    if ($ul_dom->getAttribute('class') != 'lv4') continue;
    foreach ($ul_dom->getElementsByTagName('a') as $a_dom) {
        $title = $a_dom->getAttribute('title');
        $href = $a_dom->getAttribute('href');
        if ($title == '特種委員會') break;
        if ($title == '常設委員會') continue;

        if (!file_exists(__DIR__ . "/files/{$title}")) {
            mkdir(__DIR__ . "/files/{$title}");
        }
        error_log($title);
        $doc2 = new DOMDocument;
        @$doc2->loadHTML($file_get_contents("https://www.ly.gov.tw" . $href));
        $href = null;
        foreach ($doc2->getElementsByTagName('a') as $a_dom) {
            if ($a_dom->nodeValue == '業務成果') {
                $href = $a_dom->getAttribute('href');
                break;
            }
        }
        if (is_null($href)) {
            continue;
        }

        $doc2 = new DOMDocument;
        @$doc2->loadHTML($file_get_contents("https://www.ly.gov.tw" . $href));
        $href = null;
        foreach ($doc2->getElementsByTagName('a') as $a_dom) {
            if ($a_dom->nodeValue == '業務成果') {
                $href = $a_dom->getAttribute('href');
                break;
            }
        }
        if (is_null($href)) {
            continue;
        }

        $doc2 = new DOMDocument;
        @$doc2->loadHTML($file_get_contents("https://www.ly.gov.tw" . $href));
        $href = null;
        foreach ($doc2->getElementsByTagName('a') as $a_dom) {
            if ($a_dom->nodeValue == '議事錄') {
                $href = $a_dom->getAttribute('href');
                break;
            }
        }


        if (is_null($href)) {
            continue;
        }

        $doc2 = new DOMDocument;
        @$doc2->loadHTML($file_get_contents("https://www.ly.gov.tw" . $href));
        $href = null;
        foreach ($doc2->getElementsByTagName('a') as $a_dom) {
            if ($a_dom->getAttribute('data-toggle') == 'tooltip' and preg_match('#第(\d+)屆#', $a_dom->nodeValue, $matches)) {
                $term = $matches[1];
                error_log("議事錄 第 {$term} 屆");

                $href = $a_dom->getAttribute('href');
                $doc3 = new DOMDocument;
                @$doc3->loadHTML($file_get_contents("https://www.ly.gov.tw" . $href));
                foreach ($doc3->getElementsByTagName('a') as $a_dom) {
                    if (preg_match('#(第\d+屆)?第(\d+)會期.*#', $a_dom->nodeValue, $matches)) {
                        $period = $matches[2];
                        error_log("第 {$term} 屆 第 {$period} 會期");
                        $href = $a_dom->getAttribute('href');

                        if (!file_exists(__DIR__ . "/files/{$title}/{$term}-{$period}")) {
                            mkdir(__DIR__ . "/files/{$title}/{$term}-{$period}");
                        } else {
                            continue;
                            // XXX
                        }

                        $doc4 = new DOMDocument;
                        @$doc4->loadHTML($file_get_contents("https://www.ly.gov.tw" . $href));
                        foreach ($doc4->getElementsByTagName('a') as $a_dom) {
                            if (preg_match('#File.ashx.*(docx?)$#', $a_dom->getAttribute('href'), $matches)) {
                                $ext = $matches[1];
                                $href = $a_dom->getAttribute('href');
                                $target = "{$title}/{$term}-{$period}/{$term}-{$period}.{$ext}";
                                if (!file_exists(__DIR__ . "/files/{$target}")) {
                                    error_log($target);
                                    file_put_contents(__DIR__ . "/files/{$target}", $file_get_contents("https://www.ly.gov.tw" . $href));
                                }
                            } else if (preg_match('#/Detail.aspx#', $a_dom->getAttribute('href'))) {
                                $name = trim($a_dom->nodeValue);
                                $href = $a_dom->getAttribute('href');
                                error_log("第 {$term} 屆 第 {$period} 會期 {$name}");

                                $doc5 = new DOMDocument;
                                @$doc5->loadHTML($file_get_contents("https://www.ly.gov.tw" . $href));
                                foreach ($doc5->getElementsByTagName('a') as $a_dom) {
                                    if (preg_match('#File.ashx.*(docx?)$#', $a_dom->getAttribute('href'), $matches)) {
                                        $ext = $matches[1];
                                        $href = $a_dom->getAttribute('href');
                                        $target = "{$title}/{$term}-{$period}/{$name}.{$ext}";
                                        if (!file_exists(__DIR__ . "/files/{$target}")) {
                                            error_log($target);
                                            file_put_contents(__DIR__ . "/files/{$target}", $file_get_contents("https://www.ly.gov.tw" . $href));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }



        echo $title . ' ' . $href . "\n";
    }
}
