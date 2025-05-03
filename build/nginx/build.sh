#!/bin/bash

echo This is a sample build script, if the workflow cannot be used.
echo Please customize the repository address/name for your own.


PACKAGE_REPOSITORY=proxima.goliath.hu/proxima/backend
BUILD_TAG=nginx
UID=$(id -u)
GID=$(id -g)

# ---------------------------------------------------------------------------
echo Building "${BUILD_TAG}" package ..
echo

# ---------------------------------------------------------------------------
echo Changing to project root directory...

pushd ../.. > /dev/null

# ---------------------------------------------------------------------------
echo Building assets...

pushd src > /dev/null
npm run build
popd > /dev/null

# ---------------------------------------------------------------------------
echo Determining tag name...

branch=$(git branch --show-current)
tag=${PACKAGE_TAG:-temp}

[[ $branch == dev ]] && tag=testing
[[ $branch == main ]] && tag=latest
[[ $branch == master ]] && tag=latest

# ---------------------------------------------------------------------------
echo Building image...

docker build . \
    --tag ${PACKAGE_REPOSITORY}:$tag \
    --build-arg GROUP_ID=${GID} \
    --build-arg USER_ID=${UID} \
    --file build/${BUILD_TAG}/Dockerfile \
    --platform linux/amd64,linux/arm64 \
    --provenance=false \
    --sbom=false \
#    --push \

# ---------------------------------------------------------------------------
echo Changing back to build directory...

popd > /dev/null


# ---------------------------------------------------------------------------
echo Done.
