"""
Unit tests module for this project
Just run like this :
export PYTHONPATH=/home/toto/utils ; cd ~/event ; venv ; python test_event.py
"""
import unittest
#import json

import sys
sys.path.insert(0, "/home/toto/utils")
from event import create_event

class TestLogEvent(unittest.TestCase):
    """
    Test the function create_event from module event.py.
    """

    def test_create_event(self):
        result = create_event("text","categ")
        print("result : ", result)
        # result = json.loads(result_json)
        message = result['message']
        trimmed_result=message[:6]
        print("message : ", message)
        self.assertEqual(trimmed_result, "event xxx created on..."[:6])

   
    def test_for_list_as_text(self):
        with self.assertRaises(TypeError):
            create_event([], "categ")

    def test_for_number_as_text(self):
        with self.assertRaises(TypeError):
            create_event(6, "categ")
    

if __name__ == '__main__':
    # print("start !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!")
    unittest.main()
