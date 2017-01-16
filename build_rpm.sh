#!/usr/bin/env bash
if [ ! -f `basename $0` ]; then
    echo "please run me in my container folder"
    exit 1
fi

#prepare src dir
src_dir="/root/rpmbuild/SOURCES"
mkdir -p ${src_dir}

cwd=`pwd`
prod_name=`basename ${cwd}`
version=`cat version.txt`
prod_name_with_version=${prod_name}-${version}
src_tar_gz=${prod_name_with_version}.tar.gz

#compress source
cd ${src_dir}
rm -rf ${prod_name_with_version}/
mkdir -p ${prod_name_with_version}
cp -r ${cwd}/* ${prod_name_with_version}/
tar zcf ${src_tar_gz} ${prod_name_with_version}/

spec_file=${src_dir}/${prod_name_with_version}.spec
echo "
Summary:    Generic open source php library developed by ops.best
Name:       ${prod_name}
Version:    ${version}
Release:    1
Source:     ${src_tar_gz}
License:    GPL
Packager:   qinjx
Group:      Application
URL:        https://ops.best

%description
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

#copy as latest pkg
cd /root/rpmbuild/RPMS/x86_64/
cp -f ${prod_name_with_version}-1.x86_64.rpm ${prod_name}-latest.x86_64.rpm