Composer Book
=============

This is a small senseless CLI application to render Composer as a PDF

Requirements
------------

 1. PHP >= 7.1
 2. Checkout Composer (https://github.com/composer/composer)
 3. Install wkhtml2pdf (http://wkhtmltopdf.org/)
    
Create Book
-----------

To generate the PDF:

```bash
$ bin/book
```

**Options**

- `input`: input path to Composer folder
- `output`: output path to PDF

```bash
$ bin/book --input=../composer --output=composer.pdf
```

Troubleshooting
---------------

If you run into a `Too many open files` error, increase `ulimit`:

```bash
$ ulimit -n 2048
```

Update `authors.csv` by running a command like this in the Composer repository:

```bash
git log --format='%aN' | awk '{arr[$0]++} END{for (i in arr){print "\""arr[i]"\",""\""i"\"";}}' > ../book/Resources/authors.csv
```
