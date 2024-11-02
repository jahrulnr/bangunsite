#!/bin/sh

version=3.2.0
template=https://github.com/ColorlibHQ/AdminLTE/archive/refs/tags/v$version.tar.gz
if command -v wget 2>&1 /dev/null; then
    wget $template
elif command -v curl 2>&1 /dev/null; then
    curl $template > v$version.tar.gz
else
    echo "Wget & curl not exists! please install for downloading template"
    exit 1
fi

tar -xvf v$version.tar.gz
mv AdminLTE-$version template
rm v$version.tar.gz


