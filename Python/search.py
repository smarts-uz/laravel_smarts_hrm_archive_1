import argparse
import sys
from telethon import TelegramClient
from telethon.sessions import StringSession

session = '1ApWapzMBuzwcmwVoQTrd1SMKllDXSfGRle4Pl61UEul9UIi_7-CLM95Db_Yu-JxVxfQYvBGqQNy3p3bTdZiMNLZLLsu-07vKbzZkEEOocjERJPpjiuNR6T3KBarseDBZjRfyI7HfdlMzJGlROUUg1QQ2elJKI-S6nPR-8dWMZcbkpv05feUIiPNwSLn2914VU5h8f1jvngKZgZjJz3yBDnlSibEaYs0Z3ExxF24AsqKwGni6cz2Uw9O-SXqDFWjMP1wz4wXt9DXKgBim0UhRn_Ry3GEgu929TuG1IOXyzFQi_BJD8-QnI1Ub4PQRNGrVRLU95sPKGIfuYZMznEK3ldGRrwO8-s8='
client = TelegramClient(StringSession(session), 9330195, 'adcaaf6ff60778f454ee90f3a6c26c7b')

parser = argparse.ArgumentParser(description="Please enter search key as argument for this script")
parser.add_argument('item', type=str, help="Please enter search key")
args = parser.parse_args()

list = args.item.split('::')
client.start()
print(1111111111111)
sys.exit()
message = client.iter_messages(int(list[0]))
message_id = None


for mess in message:
    if mess.message == list[1]:
        message_id = mess.id

if message_id != None:
    print('https:t.me/c/' + list[0][4:] + '/' + str(message_id))
else:
    print('Message not Found')
