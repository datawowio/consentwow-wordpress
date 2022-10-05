let i = 0;

function addConsent() {
  const contentTable = document.getElementsByTagName('tbody');

  const tag = document.createElement('tr');
  tag.classList.add('consentwow-consent-input');
  tag.id = `consentwow-consent-field-${i}`;

  // add purpose key field
  const purposeKeyCol = document.createElement('td');
  const purposeKeyInput = document.createElement("input");
  purposeKeyInput.required = true;
  purposeKeyInput.type = "text";
  purposeKeyInput.name = `consentwow_form[consents][${i}][consent_id]`;
  purposeKeyInput.classList.add('regular-text');
  purposeKeyInput.placeholder = "ID ของวัตถุประสงค์";
  purposeKeyInput.style = "width:185px;";
  purposeKeyCol.appendChild(purposeKeyInput);
  tag.appendChild(purposeKeyCol);

  // add purpose key field
  const purposeNameCol = document.createElement('td');
  const purposeNameInput = document.createElement("input");
  purposeNameInput.required = true;
  purposeNameInput.type = "text";
  purposeNameInput.name = `consentwow_form[consents][${i}][input_id]`;
  purposeNameInput.classList.add('regular-text');
  purposeNameInput.placeholder = "ชื่อวัตุประสงค์ความยินยอม";
  purposeNameCol.appendChild(purposeNameInput);

  // add remove button
  const removeButton = document.createElement("button");
  removeButton.classList.add('button');
  removeButton.style = "background-color:red; border: white; color: white; margin-left: 5px";
  removeButton.textContent = 'X';
  removeButton.id = i;
  removeButton.type = "button";
  removeButton.onclick = handleRemoveButton

  purposeNameCol.appendChild(removeButton);
  tag.appendChild(purposeNameCol);

  // add child to parent
  contentTable[0].appendChild(tag);
  i += 1;
}

function handleRemoveButton(e) {
  const id = e.target.id;
  document.getElementById(`consentwow-consent-field-${id}`).remove();
}
