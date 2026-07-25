import json
import os
import re

log_path = r"C:\Users\m s i\.gemini\antigravity\brain\0a95d0e0-7b4b-4285-9bb0-211af0b89e02\.system_generated\logs\transcript.jsonl"

def find_file_contents(filename):
    with open(log_path, 'r', encoding='utf-8') as f:
        for line_num, line in enumerate(f):
            try:
                step = json.loads(line)
                # Look in tool outputs or responses
                # A view_file response will have a file path and a output containing code lines
                step_str = json.dumps(step)
                if filename in step_str and "Total Lines:" in step_str and "Showing lines 1 to" in step_str:
                    # Let's inspect this step
                    print(f"Step {step.get('step_index')} (type: {step.get('type')}, status: {step.get('status')}) mentions {filename}")
                    # Extract output
                    tool_calls = step.get("tool_calls", [])
                    # Sometimes output is in standard fields
                    output = ""
                    if "content" in step and step["content"]:
                        output += step["content"]
                    
                    # Search for line numbers pattern
                    lines = re.findall(r"\n\s*(\d+):\s*(.*)", output)
                    if not lines:
                        # Try to look inside tool results/calls
                        for tc in tool_calls:
                            res = tc.get("result", "")
                            lines = re.findall(r"\n\s*(\d+):\s*(.*)", str(res))
                            if lines:
                                break
                    if lines:
                        # reconstruct file
                        sorted_lines = sorted(lines, key=lambda x: int(x[0]))
                        # check if we got all lines
                        max_line = int(sorted_lines[-1][0])
                        print(f"  Found reconstructed lines up to line {max_line}")
                        if max_line > 20: # just a threshold to avoid tiny outputs
                            file_content = "\n".join(l[1] for l in sorted_lines)
                            out_name = f"{filename}_reconstructed_step_{step.get('step_index')}.vue"
                            with open(out_name, 'w', encoding='utf-8') as out_f:
                                out_f.write(file_content)
                            print(f"  Wrote {out_name}")
            except Exception as e:
                pass

print("Searching for AppSidebar.vue...")
find_file_contents("AppSidebar.vue")
print("Searching for AdminLayout.vue...")
find_file_contents("AdminLayout.vue")
