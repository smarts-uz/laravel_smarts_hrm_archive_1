import sys
import argparse
import os
from pathlib import Path


def createParser ():
    parser = argparse.ArgumentParser()
    parser.add_argument ('path', nargs='?')

    return parser

if __name__ == '__main__':
    parser = createParser()
    namespace = parser.parse_args()

    # print (namespace)
    typ=''
    if namespace.path:
        for entry in os.scandir(namespace.path):
           if entry.is_dir() and entry.name == '-Theory':
               theory: Path = Path(namespace.path, entry.name)
               for entry in os.scandir(theory):
                   if entry.is_file():
                       path: Path = Path(theory, entry.name)
                       print( path )
           elif entry.is_file():
               path: Path = Path(namespace.path, entry.name)
               print( path )
    else:
        print ("There is no path argument!")

