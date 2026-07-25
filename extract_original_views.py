import json

log_path = r"C:\Users\m s i\.gemini\antigravity\brain\0a95d0e0-7b4b-4285-9bb0-211af0b89e02\.system_generated\logs\transcript.jsonl"

def extract_original_content():
    # We want to reconstruct the files as they were first viewed
    # Let's map line number -> text for each file
    sidebar_lines = {}
    layout_lines = {}
    
    with open(log_path, 'r', encoding='utf-8') as f:
        # We need to find view_file result steps.
        # When type=VIEW_FILE, the content field has the text shown.
        # But we need to know WHICH file and WHICH lines.
        # The preceding step (type=PLANNER_RESPONSE) has the tool_call info.
        current_file = None
        start_line = 1
        
        for line in f:
            try:
                step = json.loads(line)
                stype = step.get("type")
                
                if stype == "PLANNER_RESPONSE":
                    tool_calls = step.get("tool_calls", [])
                    for tc in tool_calls:
                        if tc.get("name") == "view_file":
                            args = tc.get("args", {})
                            path = args.get("AbsolutePath", "")
                            if "AppSidebar.vue" in path:
                                current_file = "sidebar"
                                start_line = int(args.get("StartLine", 1))
                            elif "AdminLayout.vue" in path:
                                current_file = "layout"
                                start_line = int(args.get("StartLine", 1))
                            else:
                                current_file = None
                elif stype == "VIEW_FILE" and step.get("status") == "DONE" and current_file:
                    content = step.get("content", "")
                    # Content is formatted with line numbers: "1: <script setup>\n2: ..."
                    # Let's parse each line
                    lines = content.split('\n')
                    for l in lines:
                        l = l.strip()
                        if not l:
                            continue
                        # format is "num: code"
                        parts = l.split(':', 1)
                        if len(parts) == 2 and parts[0].strip().isdigit():
                            num = int(parts[0].strip())
                            code = parts[1]
                            # remove leading space if any
                            if code.startswith(' '):
                                code = code[1:]
                            if current_file == "sidebar":
                                if num not in sidebar_lines: # Keep the first time it was viewed
                                    sidebar_lines[num] = code
                            elif current_file == "layout":
                                if num not in layout_lines:
                                    layout_lines[num] = code
                    current_file = None # reset
            except Exception as e:
                pass

    # Save sidebar
    if sidebar_lines:
        sorted_keys = sorted(sidebar_lines.keys())
        print(f"Sidebar: found {len(sidebar_lines)} lines, max line {sorted_keys[-1]}")
        out = []
        for i in range(1, sorted_keys[-1] + 1):
            out.append(sidebar_lines.get(i, f"// MISSING LINE {i}"))
        with open("sidebar_original_reconstructed.vue", "w", encoding="utf-8") as f_out:
            f_out.write("\n".join(out))
            
    # Save layout
    if layout_lines:
        sorted_keys = sorted(layout_lines.keys())
        print(f"Layout: found {len(layout_lines)} lines, max line {sorted_keys[-1]}")
        out = []
        for i in range(1, sorted_keys[-1] + 1):
            out.append(layout_lines.get(i, f"// MISSING LINE {i}"))
        with open("layout_original_reconstructed.vue", "w", encoding="utf-8") as f_out:
            f_out.write("\n".join(out))

extract_original_content()
