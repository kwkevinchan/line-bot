#!/bin/sh
set -e

if  [ ${USE_CP} ]
then
    echo "use cp"
    rm -r ${SERVICE_PATH}
    cp -r /app ${SERVICE_PATH}/
    chown -R 1000:1000 ${SERVICE_PATH}/
else
    echo "not use cp"
fi

exec "$@"