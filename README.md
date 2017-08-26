Dataloader List Converter
=====
**Warning: this software is in development and is not fully functional yet.** Watch the repository to be informed when the first version will be released.

When we perform operations on SFX target portfolios via the *dataloader*, we need a clean list of ISBNs or ISSNs in a tab separated file. 
Usually, we have to fetch the identifiers manually from non standardized lists in delimiter-separated formats (CSV, TSV) or Excel worksheets. The identifiers we need are sometimes merged in single fields, connected with different separators each time. This is an extract of such a list:

```
"Author"	"Editor"	"Illustrator"	"PrintISSN"	"OnlineISSN"	"PrintISBN"	"OnlineISBN"
"Centre for Independent Studies"	"Tripp"	" Gregory|Payne"	" Michael|Diodorus"	" Dimitrus"	"978-1-60692-973-5"	"978-0-511-30338-8|978-1-60876-294-1"
"GMB Publishing Ltd."	"Jervalidze"	" Liana."	"World Bank"	"978-1-905050-35-2"	"978-1-280-48056-0|978-1-905050-84-0"
```

The end result we aim for should look like this:

| ISBN | Status |
| ------ | ------ |
| 9781606929735 | ACTIVE |
| 9780511303388 | ACTIVE |
| 9781608762941 | ACTIVE |
| 9781905050352 | ACTIVE |
| 9781280480560 | ACTIVE |
| 9781905050840 | ACTIVE |

The **dataloader list converter** parses the first list in search of identifiers and generates the second **automatically**.
