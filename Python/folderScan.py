import sys
import argparse
import os
from pathlib import Path

# Get arguments of script when run in console mode
def createParser ():
    parser = argparse.ArgumentParser()
    parser.add_argument ('path', nargs='?')
    return parser

if __name__ == '__main__':
    parser = createParser()
    argument = parser.parse_args()

    # scan files in folder and if there is folder "-Theory" also scan folder -Theory
    if argument.path and os.path.exists(argument.path):
        for entry in os.scandir(argument.path):
           if entry.is_dir() and entry.name == '-Theory':
               theory: Path = Path(argument.path, entry.name)
               for entry in os.scandir(theory):
                   if entry.is_file():
                       path: Path = Path(theory, entry.name)
                       print( path )
           elif entry.is_file():
               path: Path = Path(argument.path, entry.name)
               print( path )
    else:
        print ("There is no path argument!")

