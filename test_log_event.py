"""
Test module for log_event
Just run like this :
python test_log_event.py
"""
import unittest
import json

import sys
sys.path.insert(0, "/home/toto/utils")
from log_event import log_event
from log_event import read_events

import sys

class TestLogEvent(unittest.TestCase):
    """
    Test the function log_event from module log_event.py.
    """

    def test_log_event(self):
        result_json = log_event("text","type")
        result = json.loads(result_json)
        message = result['message']
        trimmed_result=message[:10]
        self.assertEqual(trimmed_result, "event created on..."[:10])

   
    def test_for_list_as_text(self):
        with self.assertRaises(TypeError):
            log_event([], "type")

    def test_for_number_as_text(self):
        with self.assertRaises(TypeError):
            log_event(6, "type")
    

if __name__ == '__main__':
    unittest.main()
