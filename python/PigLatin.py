__author__ = 'weisern'

"""
Code academy exercise
1 Ask the user to input a word in English.
2 Make sure the user entered a valid word.
3 Convert the word from English to Pig Latin.
4 Display the translation result.

==========================
RESEARCH
4.7 on defining functions
https://docs.python.org/2/tutorial/controlflow.html

for \d regular expressions
https://docs.python.org/2/library/re.html

http://stackoverflow.com/questions/19859282/check-if-a-string-contains-a-number
==============================
"""

import re



def pyg_this( user_input= input('enter a word: ') ):
    check = has_numbers (user_input)
    if check == True:
        print ('start mixing')


def has_numbers (a_string):
    contains_num = bool( re.search( '\d', a_string) )
    if contains_num == True:
        print('Try again, no numbers allowed')
        pyg_this()
    else:
        return True