import sys, json, html

raw = sys.stdin.read().strip()
if not raw:
    print("NO CONFIG FOUND")
    sys.exit()

raw = html.unescape(raw)
try:
    data = json.loads(raw)
    svcs = data.get("services", [])
    print("Total:", len(svcs))
    wi = [s for s in svcs if s.get("image")]
    wo = [s for s in svcs if not s.get("image")]
    print("With image:", len(wi))
    print("Without image:", len(wo))
    for s in wo[:5]:
        print("  NO IMG: id=%s title=%s" % (s.get("id", "?"), s.get("title", "?")))
    if wi:
        print("  Sample URL:", wi[0]["image"][:120])
except Exception as e:
    print("JSON ERROR:", e)
    print("Start:", raw[:300])
