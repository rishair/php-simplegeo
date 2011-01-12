
# Overview
A portable PHP implementation of a SimpleGeo client.

# Installation
Include the client and you should be good to go.

	include 'SimpleGeo.php'
	
#Usage

Begin by creating an instance of the SimpleGeo client.

	$geo = new SimpleGeo($key, $secret)

## Adding a record

First create a record object:

	$record = new Record('com.layer', 'unique-id', 37.23, -80.417778);

Then pass it on to the client:

	$geo->AddRecord($record);
	
## Adding multiple records

To add multiple records create an array of Record objects and pass it into the client again. This time include a layer that all of the Records will be inserted into - the Layer parameter in the Record objects will be ignored.

	$geo->AddRecords('com.layer', $records);

## Getting and Deleting a record/history

Getting and deleting can take in either a Record object or a layer and ID. The following two methods are synonymous:

	$geo->DeleteRecord(new Record('com.layer', 'unique-id'));
	$geo->DeleteRecord('com.layer', 'unique-id');

## Getting nearby records

There are a couple of different ways to retrieve nearby records - either by IP, address or coordinates. For additional parameters pass in an associate array for the last argument

	$geo->RecordsCoord(37.23, -80.417778);
	$geo->RecordsIP('255.255.255.255');
	$geo->RecordsAddress('1234 Fantasy Street, Unicorn', array(
		'radius' => 20,
		'limit' => 30,
		'types' => 'restaurants',
		'start' => 0,
		'end' => time()
	));


## Everything else

Everything should be relatively intuitive - let me know if you find anything wrong!

# License 

(The MIT License)

Copyright (c) 2011 Rishi Ishairzay &lt;rishi [at] ishairzay [dot] com&gt;

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.