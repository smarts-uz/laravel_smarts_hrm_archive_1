import argparse

from telethon import TelegramClient

client = TelegramClient('me', 9330195, 'adcaaf6ff60778f454ee90f3a6c26c7b')

parser = argparse.ArgumentParser(description="Please enter search key as argument for this script")
parser.add_argument('item', type=str, help="Please enter search key")
args = parser.parse_args()

list = args.item.split('::')
client.start()
message = client.iter_messages(int(list[0]), search=list[1])
message_id = None
for mess in message:
    if mess.message == list[1]:
        message_id = mess.id

print('https:t.me/c/' + list[0][4:] + '/' + str(message_id))