import argparse
import asyncio
import os
import sys
from os import listdir
from os.path import join, isfile
from FastTelethonhelper import fast_download, fast_upload
from aiostream import stream
from telethon import TelegramClient

parser = argparse.ArgumentParser(description="Please enter folder path as argument for this script")
parser.add_argument('path', type=str, help="Please enter folder path")
args = parser.parse_args()

client = TelegramClient('me', 9330195, 'adcaaf6ff60778f454ee90f3a6c26c7b')

def mhtml(path):
    file = open(path)
    lines = file.readlines()
    for line in lines:
        if "Snapshot-Content-Location:" in line:
            ln = line.split(' ')
            return str(ln[1][:-1])

def txt(path):
    file = open(path)
    return file.reZzad()

def read_url(path):
    file = open(path)
    lines = file.readlines()
    url = lines[4][4:]
    return url

def search_for_url(path):
    files = listdir(path)
    if 'ALL.url' in files:
        return join(path, 'ALL.url')
    else:
        return None

def get_files(path):
    folder_list = listdir(path)
    files: list[str] = []
    for element in folder_list:
        if isfile(join(path, element)):
            files.append(element)
    return files

def to_list(item):
    return list(item)

def difference(list1, list2):
    difference = set(list2) - set(list1)
    return list(difference)

def callback(current, total):
    print('Uploaded', current, 'out of', total,'bytes: {:.2%}'.format(current / total))

def callbackdl(current, total):
    print('Downloaded', current, 'out of', total,'bytes: {:.2%}'.format(current / total))

async def script(path):
    os.system('cls')
    print('App is running')
    url_file = search_for_url(path)
    if url_file == None:
        print('В директории ' + path + ' не найден URL файл')
        sys.exit()
    else:
        url = read_url(url_file)
        list = url.split('/')
        comments = client.iter_messages(int('-100' + list[4]), reply_to=int(list[5]))
        comment_files = []
        files = get_files(path)
        text = ''
        for element in files:
            text +=element
        await client.send_message('me', text)
        async for message in comments:
            comment_files.append(message.file.name)
        to_telegram = difference(comment_files, files)
        to_storage = difference(files, comment_files)
        if len(to_telegram) != 0:
            print('Files to upload ' + str(to_telegram))
            for element in to_telegram:
                print('Sending ' + element)
                print(' ')
                split_tup = os.path.splitext(element)
                caption = ''
                if split_tup[1] == '.txt':
                    caption = txt(join(path, element))
                elif split_tup[1] == '.mhtml':
                    caption = mhtml(join(path, element))
                elif split_tup[1] == '.url':
                    continue
                else:
                    caption = element

                file = await fast_upload(client, file_location=join(path, element), progress_bar_function=callback)
                await client.send_file(int('-100' + list[4]), file, caption=caption, comment_to=int(list[5]), progress_callback=callback)
        else:
            print('No files to upload!')
        if len(to_storage) != 0:
            print('Files to download ' + str(to_storage))
            comment_files = await stream.list(comments)
            for i in range(len(comment_files)):
                if comment_files[i].file.name in to_storage:
                    print('Downloading ' + comment_files[i].file.name)
                    print(' ')
                    await fast_download(client, msg=comment_files[i], download_folder=path+'/', progress_bar_function=callbackdl)
        else:
            print('No files to download!')

client.start()

# asyncio.run(script(args.path))
loop = asyncio.get_event_loop()
loop.run_until_complete(script(args.path))
