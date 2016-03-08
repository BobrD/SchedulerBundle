Symfony scheduler bundle
========================
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/18d07f61-2426-411f-ad5a-f15acec2a5b0/big.png)](https://insight.sensiolabs.com/projects/18d07f61-2426-411f-ad5a-f15acec2a5b0)

Add the following to your root Crontab (via sudo crontab -e):

`* * * * * php /path/to/console scheduler:run 1>> /dev/null 2>&1`
