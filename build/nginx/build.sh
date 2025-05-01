#!/bin/bash

PACKAGE_REPOSITORY=gitea.goliath.hu/packages/discord-bot-goliath-backend
BUILD_TAG=nginx
PACKAGE_TAG=nginx

# ---------------------------------------------------------------------------
echo Building "${BUILD_TAG}" package with "${PACKAGE_TAG}" tag...

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

#[[ $branch == dev ]] && tag=testing
#[[ $branch == master ]] && tag=latest
#[[ $branch == features/nginx-server ]] && tag=nginx-joint

# ---------------------------------------------------------------------------
echo Building image...

docker build \
    --tag ${PACKAGE_REPOSITORY}:$tag \
    --build-arg GROUP_ID=$(id -g) \
    --build-arg USER_ID=$(id -u) \
    --file build/${BUILD_TAG}/Dockerfile \
    .
#    --progress=plain \
#    --push \

# ---------------------------------------------------------------------------
echo Changing back to build directory...

popd > /dev/null


# ---------------------------------------------------------------------------
echo Done.
