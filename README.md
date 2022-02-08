# lysub-crawler

這邊放的是將立法院常設委員會的會議記錄抓取下來，並轉成文字檔的程式，從 https://www.ly.gov.tw/Pages/List.aspx?nodeid=166 抓取列表

檔案
----
- php crawl.php
  - 從常設委員會抓取會議記錄放入 files/{委員會}/{屆次-會期}/ 資料夾內
- php to-txt.php
  - 將 files/ 內 doc 轉成 txt

授權
----
- 程式碼以 BSD License 授權

注意事項
--------
- 使用爬蟲請注意抓取頻率，以免影響伺服器運作
