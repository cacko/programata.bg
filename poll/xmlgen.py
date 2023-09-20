from xml.dom import minidom
import codecs

def _element(xml, name, parent=None, attrs=None, content=None):
    node = xml.createElement(name)
    if parent:
        parent.appendChild(node)
    if attrs:
        for key,value in attrs.items():
            node.setAttribute(str(key), str(value))
    if content:
        node.appendChild(xml.createTextNode(content))
    return node

xml  = minidom.Document()
xmlroot = _element(xml, 'poll', xml)
start = True
item_count = 1
item = None
value_count = 1
root = xmlroot
f = codecs.open('programata_short.txt', 'r', encoding='utf8')
for line in f.readlines():
    line = line.strip()
    if line.startswith('//'):
        root = _element(xml, 'group', xmlroot)
        name = _element(xml, 'name', root, content=line[2:])
        continue
    if line.startswith('\\\\'):
        root = xmlroot
        continue
    if line == '---':
        item_count += 1
        start = True
        continue
    if start:
        item = _element(xml, 'item', root, {'id' : item_count })
        name = _element(xml, 'name', item, content=line.strip())
        values = _element(xml, 'values', item)
        value_count = 1
        start = False
        continue
    value_item = _element(xml, 'value', values, {'id' : value_count}, line.strip())
    value_count += 1


f.close()
fp = codecs.open('poll.xml',"w", encoding='utf8')
xml.writexml(fp, "    ", "", "\n", "UTF-8")
fp.close()
