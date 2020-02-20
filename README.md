# travel - Personal train and bus times

Terence Eden has a cool project to turn a Nook e-reader into a personal display of the next useful trains and buses to depart from near his home: https://shkspr.mobi/blog/2020/02/turn-an-old-ereader-into-an-information-screen-nook-str/ 

I thought I'd do somethinh similar but found I had a few issues:
- I want to host it on my hosting company's server which is in a diffeeent time zone, so I had to add code to comepnsate for that
- It runs an ancient version of PHP and not all his code would work
- It really wasn't happy about slurping in the bus data from another site
- I wanted to add two different train routes from different stations as different people in our house have completely different journeys to make

So I cooked up a hideous kludge* that is half PHP, half-JavaScript. JavaScript seems to have no compunctions about slurping data from TfL.

If you want to do something similar, the index.php file works for me with PHP version 5.3.16

You'll need to modify a few things to get it to work:
