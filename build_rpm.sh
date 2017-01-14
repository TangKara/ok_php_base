#!/usr/bin/env bash
if [ ! -f `basename $0` ]; then
    echo "please run me in my container folder"
    exit 1
fi

cwd=`pwd`
prod_name=`basename ${cwd}`
src_dir="/root/rpmbuild/SOURCES"
version=`cat version.txt`
prod_name_with_version=${prod_name}-${version}
src_tar_gz=${prod_name_with_version}.tar.gz
spec_file=${src_dir}/${prod_name}-${version}.spec

#prepare src dir
mkdir -p ${src_dir}

#compress source
cd ${src_dir}
rm -rf ${prod_name_with_version}/
mkdir -p ${prod_name_with_version}
cp -r ${cwd}/* ${prod_name_with_version}/
tar zcf ${src_tar_gz} ${prod_name_with_version}/
cd ${cwd}

echo "
Summary:    OpsKitchen.com web php base rpm package
Name:       ${prod_name}
Version:    ${version}
Release:    1
Source:     ${src_tar_gz}
License:    GPL
Packager:   qinjx
Group:      Application
URL:        https://ops.best

%description
This is php base library for OpsKitchen.com

%prep
%setup -q

%build

%install
rm -rf \$RPM_BUILD_ROOT
mkdir -p \$RPM_BUILD_ROOT/${cwd}
cp -r src \$RPM_BUILD_ROOT/${cwd}

%files
${cwd}
" > ${spec_file}

#build rpm
rpmbuild -bb ${spec_file}