GET     /documents - Show all documents

GET     /documents/{document} - Show one document

POST    /documents - Create new document(uploading file name must be "file". i.e. $_FILES['file'])

DELETE  /documents/{document} - Delete document and its attachments

GET     /documents/{document}/attachment - PDF attachment resource (Download file)

GET     /documents/{document}/attachment/previews - List of images, parsed from pdf document

GET     /documents/{document}/attachment/previews/{preview} - Single image resource (Download file)

POST    /documents/{document}/attachment - Add PDF attachment to document resource. (uploading file name must be "file". i.e. $_FILES['file'])

DELETE  /documents/{document_id}/attachment/preview/{preview_id} - Delete PDF attachment to document resource