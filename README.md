# Image signer
#### The convenient way for printing some text to an image.
Helps to center single-line and multi-line text for drawing on images relative to the center of the text.
Enables correct hyphenation for a multi-line text block that does not exceed the set width of the text block and the line spacing setting.

Contains several ready fonts and colors.

### Main methods
```
addString() - add single-line
addStringBlock() - add multi-line
addColumnsStringBlock() - for separate multi-line text by n amount of columns
```
### Usefully for counting a text position
```
getXSize() - horizontal image size
getYSize() - vertical image size
```

### Result
```
getBase64() - inserting base64 image
show() - print the ready image
```
`addStringBlock` and `addColumnsStringBlock` can use multi-line string or string array for a `$stringBlock` parameter.
