#!/bin/sh

until nc -z -v -w30 app 9000
do
    echo "Waiting for application connection..."
    sleep 5
done

sleep 5

host=${TEST_HOST:-'http://127.0.0.1:8000'}

###############################################################################

url="$host/attributes/123?bar=1&test_id=1&bar_baz=SuperFoo&fooEnum=fooEnum"
expected='{"foo":"123","bar":"1","entityA":"SuperFoo","entityB":"SuperFoo","entityC":"SuperFoo","entityD":"SuperFoo"}'
result="$(curl -s $url)"

if [ $result = $expected ];
 then
   echo 'Attributes tests passed OK!'
 else
   echo "Unexpected output $result. Expected $expected"
   exit 1;
fi

###############################################################################

url="$host/query?foo=123&bar_baz=1&foo_enum=foo"
expected='{"foo":"Pre123","barBaz":true,"dto":"SomeAwesomeText","fooEnum":"foo"}'
result="$(curl -s $url)"

if [ $result = $expected ];
 then
   echo 'Query tests passed OK!'
 else
   echo "Unexpected output $result. Expected $expected"
   exit 1;
fi

###############################################################################

url="$host/body"
expected='{"foo":"123","barBaz":true,"dto":"SomeAwesomeText"}'
result="$(curl -s $url -d 'foo=123&bar_baz=1')"

if [ $result = $expected ];
 then
   echo 'Body tests passed OK!'
 else
   echo "Unexpected output $result. Expected $expected"
   exit 1;
fi

###############################################################################

url="$host/jsonrpc"
expected='{"jsonrpc":"2","method":"fooMethod","id":1,"params":{"foo":"fooParam","bar":[{"foo":1},{"foo":2}],"baz":[{"text":"SuperFoo"}]}}'
result="$(curl -s $url -H 'Content-Type: application/json' -d $expected)"

if [ $result = $expected ];
 then
   echo 'JsonRpc tests passed OK!'
 else
   echo "Unexpected output $result. Expected $expected"
   exit 1;
fi

###############################################################################

url="$host/head"
expected='{"foo":123}'
result="$(curl -s $url -H 'foo: 123')"

if [ $result = $expected ];
 then
   echo 'Head tests passed OK!'
 else
   echo "Unexpected output $result. Expected $expected"
   exit 1;
fi

###############################################################################

url="$host/discriminated"
body='{"discriminated":{"type":"bar","bar":"test"},"discriminated_collection":[{"type":"bar","bar":"test"},{"type":"foo","foo":"test"}],"enum_discriminated":{"type":"foo","foo":"test"}}'
expected='{"discriminated":{"type":"bar","bar":"test"},"discriminatedCollection":[{"type":"bar","bar":"test"},{"type":"foo","foo":"test"}],"enumDiscriminated":{"type":"foo","foo":"test"}}'
result="$(curl -s $url -H 'Content-Type: application/json' -d $body)"

if [ $result = $expected ];
 then
   echo 'Discriminated tests passed OK!'
 else
   echo "Unexpected output $result. Expected $expected"
   exit 1;
fi
