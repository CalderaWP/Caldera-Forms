#!/usr/bin/env bash
cp ../generate-zip/caldera-forms.zip caldera-forms.zip
curl -F "file=@caldera-forms.zip" https://file.io/?expires=1w > response.json
