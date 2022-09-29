Carrots
==================================================

Carrots is a simple PHP image gallery script, perfect to easily build a portfolio site.
You just have to upload it to your server along with your image files and that's it.
No code. No databases. Just you and your FTP client.

Carrots uses the [slimbox2 project](http://www.digitalia.be/software/slimbox2) and [HTML5shiv](http://code.google.com/p/html5shiv/) to enable HTML5 compatibility in IE.


Requirements
--------------------------------------

* PHP 5 or higher.
* FTP access.


How to install
--------------------------------------

1. Download Carrots and unzip it on your computer.
2. Open the settings.php file in a plain text editor (like notepad) and customize it.
3. Upload the contents to your site.
4. Upload your folders with images to the 'images' folder.


What can I customize?
--------------------------------------

**Your site name**
Change the value of *$settings['title']*.

**Your image folders path**
Set the new one in *$settings['path']*.

**Your theme**
Make a new CSS file in the styles folder. Set the name of the file you choose in *$settings['theme']*.

**The order of the menu**
Go to *$settings['order_menu']* and set it to *'ASC'* for alphabetical order or *'DESC'* for descending alphabetical order.

**The size of the thumbnails**
Set the new one on *$settings['width']* and *$settings['height']*.

**I don't want to display the menu**
Set *$settings['show_menu']* to *false* to hide it.

**I don't like the grid in the home page**
Set *$settings['show_covers_grid']* to *false* to hide it.

**I want to set a default cover image for a gallery**
Just change the image name to *_cover*.

**I want to show a gallery description**
Make a new textfile with the description in the proper folder and name it *_info.txt*.

**I want to choose the order of the files**
Go to *$settings['order_files']* and set it to *'ASC'* for alphabetical order or *'DESC'* for descending alphabetical order.

**I don't want to show the covers inside the galleries**
Change the value of *$settings['show_cover']* to false.

**I want to show the content of each gallery as a grid**
Change the value of *$settings['display_mode']* to 'grid'.

**I want/don't want to use pagination to display the content of a gallery**
Set the value of *settings['img_per_page']* to 0 for no pagination or the number of images per page that you want.


Changelog
--------------------------------------

* **v0.5** 2022-09-29

    Upgraded jQuery and slimbox2 version.

    Refactor PHP code.

* **v0.4** 2016-10-17

	Minor code improvements.

* **v0.31** 2014-05-05

	Added pagination option.

* **v0.3** 2014-04-01

	Automatic cached thumbnails. You only have to set the height and width in the settings file.

	Cover's UTF-8 bug and other minor bugs fixed.

* **v0.22** 2013-05-04

	Better thumbnails.

* **v0.21** 2013-03-18

	The cover images can be displayed or hidden inside the galleries.

	Minor compatibility fixed.

* **v0.2** 2013-01-28

	UTF-8 bug fixed: folder names now can have symbols.

 	Minor bugs fixed.

* **v0.1** 2012-11-28

	Public release


License
--------------------------------------

Copyright (c) David López

Released under the MIT License

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included
in all copies or substantial portions of the Software.

THIS SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND EXPRESS
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTIONOF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE