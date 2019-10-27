import nltk
from nltk.tokenize import sent_tokenize
from nltk.tokenize import word_tokenize
from nltk.probability import FreqDist
from nltk.corpus import stopwords
from nltk.stem import PorterStemmer
from nltk.stem.wordnet import WordNetLemmatizer
from nltk.corpus import wordnet

text="""Dying die My brother Wesley and I had purchased tickets to an all-inclusive water park and resort where we were to spend a weekend relaxing, engaging in various water activities such as swimming and canoeing, and surfing. We got to the hotel room which was basically the size of a house and found that our parents were also there. We hadn't initially planned on them being there but we were happy to have them. There were only two beds in the room though and we had to ask the staff for an extra bed so that everyone could have their own.

That night my brother and I were going to head out to the ocean but I was slightly scared that I would intentionally swim out too far into the water and be drowned by the waves. We may have gone to the water front but didn't go in too far.

The next morning my brother and I decided to go canoeing and so rented a canoe and took it out into a rocky section of the ocean where there was enough current from a river flowing into the sea to create rapids. We went straight through the rapids and must have been going between 40 and 50 miles per hour. In front of us was an even more difficult section and we had to make the decision to either take the easier route or risk something dangerous. We hastily decided to take the dangerous route. There wasn't time to think clearly about it. As we did so, we noticed that there were several groups of kids swimming in the same area that we were canoeing. Each group had an instructor with them and as we whizzed past it was all we could do to avoid hitting the children.

We made it safely through, although we wondered if we hadn't accidentally hit someone without our knowing as we were travelling so fast. Later that afternoon, we took a slower trip with the canoe up further north on a lake. It was at that time that we heard news that a kid swimming had been hit by a canoe and killed. We were sure that it was our fault and spent quite some time trying to figure out the details. Eventually we found out that the kid had died in a completely different part of the resort and it couldn't have been our fault.

While we were at the lake, we found a house that was half-submerged. We went into it to explore and found a stair case that went down to the basement which was completely under water. As we stared into the basement, a grey cat walked around the corner at the bottom of the stairs, climbed the stairs and came over to me. I picked it up as I really wanted a pet cat but saw that it was rather old and ugly. We took it back to the hotel to care for it but I knew I wouldn't keep it.

At another point in the dream, probably earlier, I was in one of the buildings of the resort where they had some exhibits and information stands. My dad was there and we were speaking with a man with dark curly hair and long beard who was telling us about each exhibit which detailed some historical article about the region. I don't remember what all we discussed.
"""

# Converts treebank to wordnet format for parts of speech
def get_wordnet_pos(treebank_tag):
    if treebank_tag.startswith('J'):
        return wordnet.ADJ
    elif treebank_tag.startswith('V'):
        return wordnet.VERB
    elif treebank_tag.startswith('N'):
        return wordnet.NOUN
    elif treebank_tag.startswith('R'):
        return wordnet.ADV
    else:
        # Default to noun
        return wordnet.NOUN

# Processes a sentence into lemmatized tokens
def process_sentence(s, stop_words, lem, stem):
    # Break into words
    words = word_tokenize(s)

    # Get parts of speech
    pos = nltk.pos_tag(words)

    # Filter out things we don't want
    filtered_pos = []
    for p in pos:
        if not p[1].startswith('PRP') and p[0] not in stop_words:
            filtered_pos.append((p[0], get_wordnet_pos(p[1])))

    # Lemmatize
    if lem:
        lemmatized_words = []
        for p in filtered_pos:
            lemmatized_words.append(lem.lemmatize(p[0], p[1]))
        return lemmatized_words
    elif stem:
        stemmatized_words = []
        for p in filtered_pos:
            stemmatized_words.append(stem.stem(p[0]))
        return stemmatized_words
    else:
        unfiltered_words = []
        for p in filtered_pos:
            unfiltered_words.append(p[0])
        return unfiltered_words

    return lemmatized_words

def takeSecond(elem):
    return elem[1]

# Finally parsed tokens after lemmatization
tokens = []

# Stop words
stop_words = stopwords.words("english")
stop_words.extend([",", ".", "!", "?", ";", ":", "n't"])

# Lemmatizer
lem = WordNetLemmatizer()

# Stemmer
stem = PorterStemmer()

# Split into sentences
sentences = sent_tokenize(text)
for s in sentences:
    tokens.extend(process_sentence(s, stop_words, None, stem))

# Get sorted frequency of words
fdist = FreqDist(tokens)

freq = []
for item in fdist.items():
    freq.append((item[0], item[1] / len(tokens)))
freq = sorted(freq, key=takeSecond)