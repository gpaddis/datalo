# Datalo: List Converter for the SFX Dataloader
[![Build Status](https://travis-ci.org/gpaddis/datalo.svg?branch=master)](https://travis-ci.org/gpaddis/datalo)

**Warning: this software is in development and is not fully functional yet.** Watch the repository to be informed when the first version will be released.

## A tool for librarians working with ExLibris' SFX
When mass updating SFX target portfolios via the *dataloader*, we need a clean, tab separated list of ISBNs or ISSNs.
Usually, we have to fetch the identifiers manually from messy lists (CSV, TSV, Excel), which have different standards for different publishers. The identifiers we need are sometimes merged in single fields, arbitrarily separated by different delimiters each time.

We start with this...

```
KBID,Title,PrintISBN,OnlineISBN,DOI
63601,Social Capital,978-1-60692-973-5,978-0-511-30338-8|978-1-60876-294-1,
103645,Georgia: Russian Foreign Energy Policy and Implications for Georgia's Energy Security (Global market briefings),978-1-905050-35-2,978-1-280-48056-0|978-1-905050-84-0,
117409,"Cellular Neural Networks and Their Applications: Proceedings of the 7th IEEE International Workshop on Cellular Neural Networks and Their Applications: Institute of Applied Physics, Johann Wolfgang Go",978-981-238-121-7,978-1-281-92935-8|978-981-277-679-2,
```

...aiming for this:

| ISBN | Status |
| ------ | ------ |
| 9781606929735 | ACTIVE |
| 9780511303388 | ACTIVE |
| 9781608762941 | ACTIVE |
| 9781905050352 | ACTIVE |
| 9781280480560 | ACTIVE |
| 9781905050840 | ACTIVE |
...

**Datalo** parses the first list in search of valid identifiers and generates the second **automatically** within seconds.

## Requirements
Datalo requires **PHP 7**.
You will also need [composer](https://getcomposer.org/) to install the script.

## Installation

Install **datalo** globally on your system with composer:
```
$ composer global require gpaddis/datalo
```
After the installation, you will be able to execute the command in any directory.


## Usage
### eBook lists
Use the command `datalo isbn` to process a list of ISBNs (passed as the first argument, in our case: `eBook_list.csv`). The second argument is the destination file you want to generate: `destination_file.txt`.
```
datalo isbn eBook_list.csv destination_file.txt
```
The script will detect the delimiter, extract all valid ISBNs from your source file and save them in the destination file.
If the file already exists, you will get a warning. You can overwrite an existing file simply setting the option `--force`:
```
datalo isbn eBook_list.csv destination_file.txt --force
```
### Journal lists
The command and options available are exactly the same as for the eBook lists, you only have to type `datalo issn` instead:
```
datalo issn journals_list.csv destination_file.txt
```
