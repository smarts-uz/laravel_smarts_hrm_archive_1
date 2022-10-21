import os
path = '.'
data ={}
a1 = []
for dirs, folder, files in os.walk(path):
    array = {}
    array['folder'] = folder
    array['files'] = files
    data[dirs] = array
import json
print(json.dumps(data))
