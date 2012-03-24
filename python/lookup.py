#Define a procedure, lookup,
#that takes two inputs:

#   - an index
#   - keyword

#The output should be a list
#of the urls associated
#with the keyword. If the keyword
#is not in the index, the output
#should be an empty list.

index = [['udacity', ['http://udacity.com', 'http://npr.org']], ['computing', ['http://acm.org']]]

#lookup(index,keyword) => ['http://udacity.com','http://npr.org']


def lookup(index,keyword):
    for elem in index:
        if elem[0] == keyword:
            return elem[1]
    return []

print lookup(index,"udacity")
