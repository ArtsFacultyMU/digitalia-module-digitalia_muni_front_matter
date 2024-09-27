# digitalia-module-digitalia_muni_front_matter
Module for creating the front matter before every PDF full text file.

## Bugfix (pdftk won't open PDF)

The module relies on system's PDFtk (the binary *pdftk* present in `$PATH`, usually `/usr/bin/` directory). The older versions of PDFtk (prior to **3.3.3** version) have a bug which causes some PDFs might not open.

The module version **3.3.3** (and higher) works fine. Both Ubuntu 20 and 22 don't have this version in their official repos. 

### How to install PDFtk 3.3.3

First, it is necessary to uninstall system wide packages:
```
aptitude purge pdftk; snap remove pdftk
```

**The recommeded way** is to use pre-built binary, see [PDFtk](https://gitlab.com/pdftk-java/pdftk). Download it (e. g. to */opt/*) and create shell script */usrb/bin/pdftk*:
```shell
#!/usr/bin/env sh

UBUNTUCP=/usr/share/java/bcprov.jar:/usr/share/java/commons-lang3.jar

java -cp $UBUNTUCP:/opt/pdftk-all.jar com.gitlab.pdftk_java.pdftk "$@"
```


**Alternative installation** (not work in ubuntu 20): It is possible to install appropriate version of PDFtk from sources. Run the commands bellow (under *root* privileges in shell):

```shell
aptitude install git default-jdk-headless ant libcommons-lang3-java libbcprov-java
git clone https://gitlab.com/pdftk-java/pdftk.git
cd pdftk && mkdir lib && ln -st lib /usr/share/java/{commons-lang3,bcprov}.jar
ant jar
java -jar build/jar/pdftk.jar --help
```

The last command is just to test if it works. Copy the following code into */usrb/bin/pdftk*:

```shell
#!/usr/bin/env sh

UBUNTUCP=/usr/share/java/bcprov.jar:/usr/share/java/commons-lang3.jar

java -cp $UBUNTUCP:/root/pdftk/build/jar/pdftk.jar com.gitlab.pdftk_java.pdftk "$@"
```


